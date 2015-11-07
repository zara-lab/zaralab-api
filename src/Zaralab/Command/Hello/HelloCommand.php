<?php
/**
 * Project: zaralab
 * Filename: HelloCommand.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 01.11.15
 */

namespace Zaralab\Command\Hello;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Zaralab\Framework\Console\Command;

class HelloCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('zaralab:hello:hello')
            ->setDescription('Greet someone - nested console command HOWTO.')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Who do you want to greet?'
            )
            ->addOption(
                'yell',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will yell in uppercase letters'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        if ($name) {
            $text = 'Hello '.$name;
        } else {
            $text = 'Hello';
        }

        if ($input->getOption('yell')) {
            $text = strtoupper($text);
        }

        $output->writeln($text);
    }
}