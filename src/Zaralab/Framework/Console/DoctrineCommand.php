<?php
/**
 * Project: zaralab
 * Filename: DoctrineCommand.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 02.11.15
 */

namespace Zaralab\Framework\Console;


abstract class DoctrineCommand extends ContainerAwareCommand
{
    /**
     * Get the doctrine entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getContainer()->get('em');
    }

    /**
     * Get the doctrine dbal connection
     *
     * @return \Doctrine\DBAL\Connection
     */
    protected function getDoctrineConnection()
    {
        return $this->getEntityManager()->getConnection();
    }
}