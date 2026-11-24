<?php
declare(strict_types=1);

namespace Yireo\CleanDatabaseTables\Console\Command;

use Magento\Framework\App\ResourceConnection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListDatabaseTables extends Command
{
    public function __construct(
        private readonly ResourceConnection $resourceConnection,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName('db:table:list');
        $this->setDescription('List all database tables');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $connection = $this->resourceConnection->getConnection();
        $tables = $connection->getTables();

        foreach ($tables as $table) {
            $output->writeln(OutputFormatter::escape((string)$table));
        }

        return Command::SUCCESS;
    }
}
