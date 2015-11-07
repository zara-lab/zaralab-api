<?php
/**
 * Project: zaralab
 * Filename: MemberManager.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 06.11.15
 */

namespace Zaralab\Service;


use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use Zaralab\Entity\Member;

/**
 * Class MemberManager
 */
class MemberManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param EntityManager $em
     * @param Logger $logger
     */
    public function __construct(EntityManager $em, Logger $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
    }

    /**
     * Get all members ordered by member id
     *
     * @return array|Member[]
     */
    public function getAll()
    {
        return $this->getListBy([], ['id' => 'asc']);
    }

    /**
     * Get all active members, ordered by first name by default
     *
     * @param array $orderBy
     *
     * @return array|Member[]
     */
    public function getAllByActive(array $orderBy = ['firstName' => 'asc'])
    {
        return $this->getListBy(['enabled' => true], $orderBy);
    }

    /**
     * Get member by id
     *
     * @param int $id
     *
     * @return null|Member
     */
    public function get($id)
    {
        if (!preg_match('/\d+/', $id)) {
            throw new \InvalidArgumentException('Invalid member id, integer expected');
        }

        return $this->getOneBy(['id' => $id]);
    }

    /**
     * @param int $id
     * @return null|Member
     */
    public function getByActive($id)
    {
        if (!preg_match('/\d+/', $id)) {
            throw new \InvalidArgumentException('Invalid member id, integer expected');
        }

        return $this->getOneBy(['id' => $id, 'enabled' => true]);
    }

    /**
     * Get member list by given criteria
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     *
     * @return array|Member[]
     */
    public function getListBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $em = $this->getDoctrine();
        $repo = $em->getRepository('Zaralab\Entity\Member');
        $this->logger->info('Member table queried (list)', [
            'criteria' => $criteria,
            'orderBy' => $orderBy,
            'limit' => $limit,
            'offset' => $offset
        ]);

        return $repo->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Get single member by given criteria
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @return null|Member
     */
    public function getOneBy(array $criteria, array $orderBy = null)
    {
        $em = $this->getDoctrine();
        $repo = $em->getRepository('Zaralab\Entity\Member');
        $this->logger->info('Member table queried (one)', [
            'criteria' => $criteria,
            'orderBy' => $orderBy
        ]);

        return $repo->findOneBy($criteria, $orderBy);
    }

    /**
     * Get Monolog logger shorthand.
     *
     * @return Logger
     */
    public function getMonolog()
    {
        return $this->logger;
    }

    /**
     * Get Doctrine Entity Manager shorthand.
     *
     * @return EntityManager
     */
    public function getDoctrine()
    {
        return $this->em;
    }
}