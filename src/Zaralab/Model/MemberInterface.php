<?php
/**
 * Project: zaralab
 * Filename: MemberInterface.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 10.11.15
 */

namespace Zaralab\Model;


use Symfony\Component\Security\Core\User\AdvancedUserInterface;

interface MemberInterface extends AdvancedUserInterface
{
    const ROLE_DEFAULT = 'ROLE_USER';
    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_USER = 'ROLE_USER';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getPlainPassword();

    /**
     * Set encoded password.
     *
     * @param string $password
     */
    public function setPassword($password);
}