<?php
/**
 * Project: zaralab
 * Filename: ContainerAwareInterface.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 05.11.15
 */

namespace Zaralab\Framework\Di;


use Interop\Container\ContainerInterface;

/**
 * Implemented by classes that depends on a Container.
 *
 * @package Zaralab\Framework
 */
interface ContainerAwareInterface
{
    /**
     * Set the container.
     *
     * @param ContainerInterface|null $container A container instance
     */
    public function setContainer(ContainerInterface $container = null);
}