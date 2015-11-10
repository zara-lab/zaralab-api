<?php
/**
 * Project: zaralab
 * Filename: App.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 01.11.15
 */

namespace Zaralab\Framework\Console;

use Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\LatestCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand;
use Interop\Container\ContainerInterface;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Shell;
use Symfony\Component\Finder\Finder;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Console\Helper\DebugFormatterHelper;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

class App extends BaseApplication
{
    private $container;
    private $commandsRegistered = false;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container App config
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct($this->container->get('APPNAME'), $this->container->get('VERSION'));

        $this->getDefinition()->addOption(
            new InputOption('--shell', '-s', InputOption::VALUE_NONE, 'Launch the shell.')
        );
        $this->getDefinition()->addOption(
            new InputOption(
                '--process-isolation',
                null,
                InputOption::VALUE_NONE,
                'Launch commands from shell as a separate process.'
            )
        );
        $this->getDefinition()->addOption(
            new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', $this->container->get('ENV'))
        );
        $this->getDefinition()->addOption(
            new InputOption('--no-debug', null, InputOption::VALUE_NONE, 'Switches off debug mode.')
        );
    }

    /**
     * Set console helpers
     *
     * @return $this
     */
    protected function setHelpers()
    {
        $entityManager = $this->getContainer()->get('em');

        $helperSet = new HelperSet(
            [
                'db' => new ConnectionHelper($entityManager->getConnection()),
                'em' => new EntityManagerHelper($entityManager),
                'question' => new QuestionHelper(),
                'formatter' => new FormatterHelper(),
                'process' => new ProcessHelper(),
                'debug_formatter' => new DebugFormatterHelper(),
            ]
        );

        $this->setHelperSet($helperSet);

        return $this;
    }

    /**
     * Add Doctrine commands
     *
     * @return $this
     */
    public function addOrmCommands()
    {
        ConsoleRunner::addCommands($this);

        return $this;
    }

    /**
     * Add Doctrine migrations commands
     *
     * @return $this
     */
    public function addOrmMigrationsCommands()
    {
        $this->addCommands(
            [
                new ExecuteCommand(),
                new GenerateCommand(),
                new LatestCommand(),
                new MigrateCommand(),
                new StatusCommand(),
                new VersionCommand(),
                new DiffCommand()
            ]
        );

        return $this;
    }

    /**
     * Runs the current application.
     *
     * @param InputInterface $input An Input instance
     * @param OutputInterface $output An Output instance
     *
     * @return int 0 if everything went fine, or an error code
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        if (!$this->commandsRegistered) {
            $this->registerCommands();

            $this->commandsRegistered = true;
        }

        $container = $this->getContainer();

        foreach ($this->all() as $command) {
            if ($command instanceof ContainerAwareCommand) {
                $command->setContainer($container);
            }
        }

        if (true === $input->hasParameterOption(array('--shell', '-s'))) {
            $shell = new Shell($this);
            $shell->setProcessIsolation($input->hasParameterOption(array('--process-isolation')));
            $shell->run();

            return 0;
        }

        return parent::doRun($input, $output);
    }

    /**
     * Register application helpers and commands
     */
    protected function registerCommands()
    {
        // Defaults
        $this->setHelpers()
            ->addOrmCommands()
            ->addOrmMigrationsCommands();

        // Current application commands
        $ns = $this->getAppNamespace();
        $path = $this->getApplicationBase($ns).'/Command';

        if (!is_dir($path)) {
            return;
        }

        $finder = new Finder();
        $finder->files()->name('*Command.php')->in($path);

        $ns .= '\\Command';
        foreach ($finder as $file) {
            /** @var \Symfony\Component\Finder\SplFileInfo  $file */
            $_ns = $ns;
            $relativePath = $file->getRelativePath();
            if ($relativePath) {
                $_ns .= '\\'.strtr($relativePath, '/', '\\');
            }

            $class = $_ns.'\\'.$file->getBasename('.php');

            $r = new \ReflectionClass($class);
            if ($r->isSubclassOf('Symfony\\Component\\Console\\Command\\Command')
                    && !$r->isAbstract()
                    && !$r->getConstructor()->getNumberOfRequiredParameters()
            ) {
                $this->add($r->newInstance());
            }
        }
    }

    /**
     * Gets the application namespace.
     *
     * @return string namespace
     */
    public function getAppNamespace()
    {
        $class = get_class($this);

        return substr($class, 0, strpos($class, '\\'));
    }

    /**
     * Get application source base path
     *
     * @param string $ns App namespace
     * @return string base path
     */
    public function getApplicationBase($ns)
    {
        return $this->container->get('PROJECT_ROOT').'/src/'.$ns;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }
}