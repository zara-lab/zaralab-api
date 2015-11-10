<?php
/**
 * Project: zaralab
 * Filename: MemberProvider.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 10.11.15
 */

namespace Zaralab\Security;


use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Zaralab\Model\MemberInterface;
use Zaralab\Service\MemberManager;

class MemberProvider implements UserProviderInterface
{
    /**
     * @var MemberManager
     */
    protected $memberManager;
    /**
     * Constructor.
     *
     * @param MemberManager $memberManager
     */
    public function __construct(MemberManager $memberManager)
    {
        $this->memberManager = $memberManager;
    }

    /**
     * Override base provider behaviour - alias of email loader
     *
     * @param string $email
     * @return \Zaralab\Entity\Member
     */
    public function loadUserByUsername($email)
    {
        return $this->memberManager->findMemberByEmail($email);
    }

    /**
     * @inheritDoc
     */
    public function refreshUser(UserInterface $member)
    {
        if (!$member instanceof MemberInterface) {
            throw new UnsupportedUserException(sprintf('Expected an instance of Zaralab\Model\MemberInterface, but got "%s".', get_class($member)));
        }
        if (!$this->supportsClass(get_class($member))) {
            throw new UnsupportedUserException(sprintf('Expected an instance of %s, but got "%s".', $this->memberManager->getClass(), get_class($member)));
        }
        if (null === $reloadedMember = $this->memberManager->findMemberById($member->getId())) {
            throw new UsernameNotFoundException(sprintf('User with ID "%s" could not be reloaded.', $member->getId()));
        }
        return $reloadedMember;
    }

    /**
     * @inheritDoc
     */
    public function supportsClass($class)
    {
        $memberClass = $this->memberManager->getClass();
        return $memberClass === $class || is_subclass_of($class, $memberClass);
    }
}