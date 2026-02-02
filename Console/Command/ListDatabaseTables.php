<?php

namespace Yireo\CleanDatabaseTables\Console\Command;

use Magento\Framework\App\ResourceConnection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListDatabaseTables extends Command
{
    public function __construct(
        private ResourceConnection $resourceConnection,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure()
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
            $output->writeln($table);
        }

        return Command::SUCCESS;
    }
}
