<?php
/**
 * Project: zaralab
 * Filename: LoadDatafixturesCommand.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 06.11.15
 */

namespace Zaralab\Command;


use Zaralab\Framework\Console\DoctrineCommand;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use InvalidArgumentException;
use Doctrine\Common\DataFixtures\Loader as DataFixturesLoader;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Load data fixtures from bundles.
 * Based on Symfony2 implementation.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Jonathan H. Wage <jonwage@gmail.com>
 */
class LoadDataFixturesCommand extends DoctrineCommand
{
    protected function configure()
    {
        $this
            ->setName('fixtures:load')
            ->setDescription('Load data fixtures to your database.')
            ->addOption('append', null, InputOption::VALUE_NONE, 'Append the data fixtures instead of deleting all data from the database first.')
            ->addOption('truncate', null, InputOption::VALUE_NONE, 'Purge data by using a database-level TRUNCATE statement')
            ->addOption('truncate-only', null, InputOption::VALUE_NONE, 'Purge data by using a database-level TRUNCATE statement WITHOUT loading data fixtures')
            ->setHelp(<<<EOT
The <info>doctrine:fixtures:load</info> command loads data fixtures from your bundles:

  <info>./app/console fixtures:load</info>

If you want to append the fixtures instead of flushing the database first you can use the <info>--append</info> option:

  <info>./app/console doctrine:fixtures:load --append</info>

By default Doctrine Data Fixtures uses DELETE statements to drop the existing rows from
the database. If you want to use a TRUNCATE statement instead you can use the <info>--truncate</info> flag:

  <info>./app/console doctrine:fixtures:load --truncate</info>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getEntityManager();

        if ($input->isInteractive() && !$input->getOption('append')) {
            if (!$this->askConfirmation($input, $output, '<question>Careful, database will be purged. Do you want to continue y/N ?</question>', false)) {
                return;
            }
        }

        $app = $this->getApp();
        $path = $app->getApplicationBase($app->getAppNamespace()).'/DataFixture';

        $loader = new DataFixturesLoader();
        $loader->loadFromDirectory($path);
        $fixtures = $loader->getFixtures();
        if (!$fixtures) {
            throw new InvalidArgumentException(
                sprintf('Could not find any fixtures to load in: %s', "\n\n- $path")
            );
        }
        $purger = new ORMPurger($em);
        if ($input->getOption('truncate-only')) {
            $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
            $purger->purge();
            exit(0);
        }

        $purger->setPurgeMode($input->getOption('truncate') ? ORMPurger::PURGE_MODE_TRUNCATE : ORMPurger::PURGE_MODE_DELETE);
        $executor = new ORMExecutor($em, $purger);
        $executor->setLogger(function ($message) use ($output) {
            $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', $message));
        });
        $executor->execute($fixtures, $input->getOption('append'));
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param string          $question
     * @param bool            $default
     *
     * @return bool
     */
    private function askConfirmation(InputInterface $input, OutputInterface $output, $question, $default)
    {
        $questionHelper = $this->getHelperSet()->get('question');
        $question = new ConfirmationQuestion($question, $default);

        return $questionHelper->ask($input, $output, $question);
    }
}
