<?php
declare(strict_types=1);

namespace Headsnet\DoctrineDbCopyBundle\Console;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Result;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'headsnet:copy-db',
    description: 'Copies a database using only SQL commands, without requiring e.g. mysqldump'
)]
final class CopyDbCommand extends Command
{
    public function __construct(
        private readonly Connection $connection
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'source',
                InputArgument::REQUIRED,
                'The name of the source database'
            )
            ->addArgument(
                'destination',
                InputArgument::REQUIRED,
                'The name of the destination database. For multiple database copies, comma separate the names.'
            )
        ;
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sourceDb = $input->getArgument('source');
        $destination = $input->getArgument('destination');

        foreach (explode(',', $destination) as $destDb)
        {
            $output->writeln(sprintf('Copying database %s to %s', $sourceDb, $destDb));
            $this->copyDatabase($destDb, $sourceDb);
        }

        return Command::SUCCESS;
    }

    /**
     * @throws Exception
     */
    private function copyDatabase(mixed $destinationDb, mixed $sourceDb): void
    {
        // Create target database
        $this->query(sprintf('DROP DATABASE IF EXISTS %s', $destinationDb));
        $this->query(sprintf('CREATE DATABASE %s', $destinationDb));

        // Disable foreign key checks
        $this->query('SET FOREIGN_KEY_CHECKS = 0');

        // Load all tables names from source database
        $result = $this->query('SHOW TABLES');
        $tables = $result->fetchAllAssociative();
        $tables = array_map(fn(array $table): string => current($table), $tables);

        // Loop over tables and...
        foreach ($tables as $table) {
            // create tables in the new database
            $this->query(sprintf('CREATE TABLE %s.%s LIKE %s.%s', $destinationDb, $table, $sourceDb, $table));

            // copy table data to new database
            $this->query(sprintf('INSERT INTO %s.%s SELECT * FROM %s.%s', $destinationDb, $table, $sourceDb, $table));
        }

        // Re-enable foreign key checks
        $this->query('SET FOREIGN_KEY_CHECKS = 1');
    }

    /**
     * @throws Exception
     */
    private function query(string $sql): Result
    {
        $stmt = $this->connection->prepare($sql);

        return $stmt->executeQuery();
    }
}
