<?php
declare(strict_types=1);

namespace Yireo\CleanDatabaseTables\Console\Command;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\Declaration\Schema\Declaration\ReaderComposite;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class RemoveDatabaseTable extends Command
{
    public function __construct(
        private readonly ResourceConnection $resourceConnection,
        private readonly ReaderComposite $schemaReader,
        private readonly LoggerInterface $logger,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName('db:table:remove');
        $this->setDescription('Remove a database table');
        $this->addArgument('table', InputArgument::REQUIRED, 'Table name');
        $this->addOption(
            'force',
            'f',
            InputOption::VALUE_NONE,
            'Skip confirmation and allow removing tables declared by enabled modules'
        );
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tableName = (string)$input->getArgument('table');
        $escapedTableName = OutputFormatter::escape($tableName);
        $force = (bool)$input->getOption('force');
        $connection = $this->resourceConnection->getConnection();

        if (!in_array($tableName, $connection->getTables(), true)) {
            $output->writeln('<error>Table "' . $escapedTableName . '" does not exist in the current database</error>');
            return Command::FAILURE;
        }

        if (!$force && $this->isDeclaredTable($tableName)) {
            $output->writeln('<error>Table "' . $escapedTableName . '" is declared by an enabled module. '
                . 'Use --force to remove it anyway</error>');
            return Command::FAILURE;
        }

        if (!$force) {
            if (!$input->isInteractive()) {
                $output->writeln('<error>Refusing to remove a table without confirmation. Use --force</error>');
                return Command::FAILURE;
            }

            /** @var QuestionHelper $questionHelper */
            $questionHelper = $this->getHelper('question');
            $question = new ConfirmationQuestion('Drop table "' . $escapedTableName . '"? [y/N] ', false);
            if (!$questionHelper->ask($input, $output, $question)) {
                $output->writeln('<comment>Aborted</comment>');
                return Command::FAILURE;
            }
        }

        $context = ['table' => $tableName, 'user' => get_current_user(), 'force' => $force];
        $this->logger->notice('Dropping database table via db:table:remove', $context);
        $connection->dropTable($tableName);
        $this->logger->notice('Dropped database table via db:table:remove', $context);

        $output->writeln('<info>Dropped table "' . $escapedTableName . '"</info>');
        return Command::SUCCESS;
    }

    private function isDeclaredTable(string $tableName): bool
    {
        $schema = $this->schemaReader->read('all');
        $declaredTables = is_array($schema['table'] ?? null) ? array_keys($schema['table']) : [];

        foreach ($declaredTables as $declaredTable) {
            if ($this->resourceConnection->getTableName((string)$declaredTable) === $tableName) {
                return true;
            }
        }

        return false;
    }
}
