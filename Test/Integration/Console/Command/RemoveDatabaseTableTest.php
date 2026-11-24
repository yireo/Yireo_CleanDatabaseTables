<?php
declare(strict_types=1);

namespace Yireo\CleanDatabaseTables\Test\Integration\Console\Command;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Console\CommandListInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Yireo\CleanDatabaseTables\Console\Command\RemoveDatabaseTable;

class RemoveDatabaseTableTest extends TestCase
{
    private const TEMPORARY_TABLE = 'yireo_cleandatabasetables_test';

    private string $tableName;

    protected function setUp(): void
    {
        $resourceConnection = Bootstrap::getObjectManager()->get(ResourceConnection::class);
        $this->tableName = $resourceConnection->getTableName(self::TEMPORARY_TABLE);

        $connection = $this->getConnection();
        $connection->dropTable($this->tableName);
        $table = $connection->newTable($this->tableName)
            ->addColumn('id', Table::TYPE_INTEGER, null, ['nullable' => false, 'primary' => true]);
        $connection->createTable($table);
    }

    protected function tearDown(): void
    {
        $this->getConnection()->dropTable($this->tableName);
    }

    public function testCommandIsRegistered(): void
    {
        $commands = Bootstrap::getObjectManager()->get(CommandListInterface::class)->getCommands();
        $command = $commands['yireo_cleandatabasetables_remove_database_table'] ?? null;

        $this->assertInstanceOf(RemoveDatabaseTable::class, $command);
        $this->assertSame('db:table:remove', $command->getName());
    }

    public function testFailsWithMissingTable(): void
    {
        $commandTester = $this->createCommandTester();
        $commandTester->execute(['table' => 'yireo_missing_table', '--force' => true]);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());
        $this->assertStringContainsString('does not exist', $commandTester->getDisplay());
    }

    public function testFailsWithTableInOtherSchema(): void
    {
        $schema = (string)$this->getConnection()->fetchOne('SELECT DATABASE()');
        $commandTester = $this->createCommandTester();
        $commandTester->execute(['table' => $schema . '.' . $this->tableName, '--force' => true]);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());
        $this->assertTrue($this->getConnection()->isTableExists($this->tableName));
    }

    public function testFailsWithDeclaredTableWithoutForce(): void
    {
        $resourceConnection = Bootstrap::getObjectManager()->get(ResourceConnection::class);
        $tableName = $resourceConnection->getTableName('core_config_data');

        $commandTester = $this->createCommandTester();
        $commandTester->setInputs(['y']);
        $commandTester->execute(['table' => $tableName]);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());
        $this->assertStringContainsString('declared by an enabled module', $commandTester->getDisplay());
        $this->assertTrue($this->getConnection()->isTableExists($tableName));
    }

    public function testFailsWithoutForceInNonInteractiveMode(): void
    {
        $commandTester = $this->createCommandTester();
        $commandTester->execute(['table' => $this->tableName], ['interactive' => false]);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());
        $this->assertStringContainsString('without confirmation', $commandTester->getDisplay());
        $this->assertTrue($this->getConnection()->isTableExists($this->tableName));
    }

    public function testAbortsWhenNotConfirmed(): void
    {
        $commandTester = $this->createCommandTester();
        $commandTester->setInputs(['n']);
        $commandTester->execute(['table' => $this->tableName]);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());
        $this->assertTrue($this->getConnection()->isTableExists($this->tableName));
    }

    public function testDropsTableWhenConfirmed(): void
    {
        $commandTester = $this->createCommandTester();
        $commandTester->setInputs(['y']);
        $commandTester->execute(['table' => $this->tableName]);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString('Dropped table', $commandTester->getDisplay());
        $this->assertFalse($this->getConnection()->isTableExists($this->tableName));
    }

    public function testDropsTableWithForce(): void
    {
        $commandTester = $this->createCommandTester();
        $commandTester->execute(['table' => $this->tableName, '--force' => true], ['interactive' => false]);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertFalse($this->getConnection()->isTableExists($this->tableName));
    }

    private function createCommandTester(): CommandTester
    {
        $command = Bootstrap::getObjectManager()->create(RemoveDatabaseTable::class);
        $application = new Application();
        $application->add($command);

        return new CommandTester($command);
    }

    private function getConnection(): AdapterInterface
    {
        return Bootstrap::getObjectManager()->get(ResourceConnection::class)->getConnection();
    }
}
