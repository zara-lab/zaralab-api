<?php
/**
 * Project: zaralab
 * Filename: ContainerAware.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 05.11.15
 */

namespace Zaralab\Framework\Di;


use Interop\Container\ContainerInterface;

abstract class ContainerAware implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @inheritDoc
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}