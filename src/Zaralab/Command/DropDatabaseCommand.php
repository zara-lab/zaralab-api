<?php
/**
 * Project: zaralab
 * Filename: ContainerAwareCommand.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 01.11.15
 */

namespace Zaralab\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\DriverManager;
use Zaralab\Framework\Console\DoctrineCommand;

/**
 * Database tool allows you to easily create your configured databases.
 *
 * Based on DoctrineBundle CreateDoctrineDatabaseCommand
 */
class DropDatabaseCommand extends DoctrineCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('database:drop')
            ->setDescription('Drops the configured databases')
            ->addOption('force', null, InputOption::VALUE_NONE)
            ->setHelp(<<<EOT
The <info>database:drop</info> command drops the application database:

<info>php app/console database:drop</info>
EOT
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getApp()->getContainer()->get('logger')->critical('Dropping database attempt');
        $env = $this->getApp()->getContainer()->get('ENV');
        if ($env != 'test' && !$input->getOption('force')) {
            throw new \Exception(
                sprintf('Access denied - can not drop database in "%s" environment', $env)
            );
        }
        
        $connection = $this->getDoctrineConnection();

        $params = $connection->getParams();
        if (isset($params['master'])) {
            $params = $params['master'];
        }

        $name = isset($params['path']) ? $params['path'] : (isset($params['dbname']) ? $params['dbname'] : false);
        if (!$name) {
            throw new \InvalidArgumentException("Connection does not contain a 'path' or 'dbname'.");
        }
        unset($params['dbname']);

        $tmpConnection = DriverManager::getConnection($params);

        // Only quote if we don't have a path
        if (!isset($params['path'])) {
            $name = $tmpConnection->getDatabasePlatform()->quoteSingleIdentifier($name);
        }

        $error = false;
        try {
            $tmpConnection->getSchemaManager()->dropDatabase($name);
            $output->writeln(sprintf('<info>Dropped database <comment>%s</comment></info>', $name));
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>Could not drop database <comment>%s</comment></error>', $name));
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            $error = true;
        }

        $tmpConnection->close();

        return $error ? 1 : 0;
    }
}
