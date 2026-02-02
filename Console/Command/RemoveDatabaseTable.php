<?php

namespace Yireo\CleanDatabaseTables\Console\Command;

use Magento\Framework\App\ResourceConnection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveDatabaseTable extends Command
{
    public function __construct(
        private ResourceConnection $resourceConnection,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('db:table:remove');
        $this->setDescription('Remove a database table');
        $this->addArgument('table', InputArgument::REQUIRED, 'Table name');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tableName = $input->getArgument('table');
        $this->resourceConnection->getConnection()->dropTable($tableName);
        return Command::SUCCESS;
    }
}
