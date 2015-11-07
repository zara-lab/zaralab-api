<?php
/**
 * Project: zaralab
 * Filename: ContainerAwareCommand.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 01.11.15
 */

namespace Zaralab\Framework\Console;

use Interop\Container\ContainerInterface;
use Zaralab\Framework\Di\ContainerAwareInterface;


/**
 * Container Aware Command.
 */
abstract class ContainerAwareCommand extends Command implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface|null
     */
    private $container;

    /**
     * @return ContainerInterface
     * @throws \LogicException
     */
    protected function getContainer()
    {
        if (null === $this->container) {
            /** @var App $application */
            $application = $this->getApp();

            if (null === $application) {
                throw new \LogicException('The container cannot be retrieved.');
            }

            $this->container = $application->getContainer();
        }

        return $this->container;
    }

    /**
     * Sets the container
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
