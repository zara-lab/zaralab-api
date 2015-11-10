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
use Doctrine\ORM\EntityRepository;
use Monolog\Logger;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Zaralab\Model\MemberInterface;

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
     * @var EncoderFactoryInterface
     */
    protected $encoder;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @param EntityManager $em
     * @param PasswordEncoderInterface $encoder
     * @param Logger $logger
     */
    public function __construct(EntityManager $em, PasswordEncoderInterface $encoder, Logger $logger)
    {
        $this->em = $em;
        $this->encoder = $encoder;
        $this->logger = $logger;
    }

    /**
     * @return EntityRepository
     */
    public function getRepository()
    {
        if (null === $this->repository) {
            $this->repository = $this->em->getRepository($this->getClass());
        }

        return $this->repository;
    }

    /**
     * Finds a member by email
     *
     * @param string $email
     *
     * @return MemberInterface
     */
    public function findMemberByEmail($email)
    {
        return $this->findMemberBy(array('email' => $email));
    }

    /**
     * Get member by id
     *
     * @param int $id
     *
     * @return null|MemberInterface
     */
    public function findMemberById($id)
    {
        return $this->findMemberBy(['id' => $id]);
    }

    /**
     * @param int $id
     * @return null|MemberInterface
     */
    public function findMemberByActive($id)
    {
        return $this->findMemberBy(['id' => $id, 'enabled' => true]);
    }

    /**
     * Get all members ordered by member id
     *
     * @return array|MemberInterface[]
     */
    public function getMembers()
    {
        return $this->findMembersBy([], ['id' => 'asc']);
    }

    /**
     * Get all active members, ordered by first name by default
     *
     * @param array $orderBy
     *
     * @return array|MemberInterface[]
     */
    public function findMembersByActive(array $orderBy = ['firstName' => 'asc'])
    {
        return $this->findMembersBy(['enabled' => true], $orderBy);
    }

    /**
     * Get member collection by given criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     *
     * @return array|MemberInterface[]
     */
    public function findMembersBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $this->logger->info('Member table queried (list)', [
            'criteria' => $criteria,
            'orderBy' => $orderBy,
            'limit' => $limit,
            'offset' => $offset
        ]);

        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Get single member by given criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @return null|MemberInterface
     */
    public function findMemberBy(array $criteria, array $orderBy = null)
    {
        $this->logger->info('Member table queried (one)', [
            'criteria' => $criteria,
            'orderBy' => $orderBy
        ]);

        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * Reload member from the DB.
     *
     * @param MemberInterface $member
     */
    public function reloadMember(MemberInterface $member)
    {
        $this->em->refresh($member);
    }

    /**
     * Updates member password
     *
     * @param MemberInterface $member
     */
    public function updatePassword(MemberInterface $member)
    {
        if (0 !== strlen($password = $member->getPlainPassword())) {
            $encoder = $this->getEncoder();
            $member->setPassword($encoder->encodePassword($password, $member->getSalt()));
            $member->eraseCredentials();
        }
    }

    /**
     * Updates a member.
     *
     * @param MemberInterface $member
     * @param bool $andFlush Whether to flush the changes (default true)
     */
    public function updateMember(MemberInterface $member, $andFlush = true)
    {
        $this->updatePassword($member);
        $this->em->persist($member);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * Deletes a member.
     *
     * @param MemberInterface $member
     */
    public function deleteMember(MemberInterface $member)
    {
        $this->em->remove($member);
        $this->em->flush();
    }

    public function getClass()
    {
        return 'Zaralab\Entity\Member';
    }

    /**
     * Returns an empty member instance
     *
     * @return MemberInterface
     */
    public function createMember()
    {
        $class = $this->getClass();
        $user = new $class;

        return $user;
    }

    /**
     * Get password encoder
     *
     * @return PasswordEncoderInterface
     */
    public function getEncoder()
    {
        return $this->encoder;
    }
}