<?php
declare(strict_types=1);

namespace Yireo\CleanDatabaseTables\Test\Integration\Console\Command;

use Magento\Framework\App\ResourceConnection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Yireo\CleanDatabaseTables\Console\Command\ListDatabaseTables;

class ListDatabaseTablesTest extends TestCase
{
    public function testListsTables(): void
    {
        $tableName = Bootstrap::getObjectManager()->get(ResourceConnection::class)->getTableName('core_config_data');

        $commandTester = new CommandTester(Bootstrap::getObjectManager()->create(ListDatabaseTables::class));
        $commandTester->execute([]);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString($tableName, $commandTester->getDisplay());
    }
}
