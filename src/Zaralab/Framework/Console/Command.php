<?php
/**
 * Project: zaralab
 * Filename: Command.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 07.11.15
 */

namespace Zaralab\Framework\Console;

use Symfony\Component\Console\Command\Command as BaseCommand;

/**
 * Class Command
 * Patch the application getterS
 */
class Command extends BaseCommand
{
    /**
     * @return App
     */
    public function getApp()
    {
        return $this->getApplication();
    }
}