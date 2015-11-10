<?php
/**
 * Project: zaralab
 * Filename: SecurityManager.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 10.11.15
 */

namespace Zaralab\Service;


use Interop\Container\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserChecker;
use Symfony\Component\Security\Core\Util\SecureRandom;
use Symfony\Component\Security\Core\Util\StringUtils;
use Zaralab\Framework\Di\ContainerAware;
use Zaralab\Model\Member;

class SecurityManager extends ContainerAware
{
    /**
     * Not much use currently with low level integration
     *
     * @link https://github.com/symfony/symfony/issues/15207 Related discussion.
     */
    const PROVIDER_KEY = 'api';

    /**
     * Constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function authenticate($email, $password)
    {
        /** @var MemberManager $mm */
        $mm = $this->container['member.manager'];
        /** @var Member $member */
        $member = $mm->findMemberByEmail($email);
        /** @var MessageDigestPasswordEncoder $encoder */
        $encoder = $this->container['security.encoder'];
        /** @var UserChecker $userChecker */
        $userChecker = $this->container['security.user_checker'];

        try {
            if (!$member) {
            } elseif ($member) {
                $userChecker->checkPreAuth($member);
                if($encoder->isPasswordValid($member->getPassword(), $password, $member->getSalt())) {
                    $encodedPassword = $encoder->encodePassword($password, $member->getSalt());
                    $match = StringUtils::equals($member->getPassword(), $encodedPassword);
                    $userChecker->checkPostAuth($member);

                    if ($match) {
                        $authenticatedToken = new UsernamePasswordToken($member, $password, self::PROVIDER_KEY, $member->getRoles());
                        $this->container['security.token_storage']->setToken($authenticatedToken);
                    }
                }
            }
        } catch (AuthenticationException $failed) {
            $this->container->get('logger')->error(sprintf(
                'Authentication failed for user "%s" (using password - "%s")',
                $email,
                !empty($password) ? 'yes' : 'no'
            ), ['email' => $email, 'description' => $failed->getMessage()]);
            throw new $failed;
        }

        if (null === $this->container['member']) {
            throw new AuthenticationCredentialsNotFoundException('Authentication credentials could not be found.', 400);
        }


        return $this->container['member'];
    }

    public function authenticateOld($email, $password)
    {
        /** @var MemberManager $mm */
        $mm = $this->container['member.manager'];
        $member = $mm->findMemberByEmail($email);

        try {
            $unauthenticatedToken = new UsernamePasswordToken(
                $member,
                $password,
                self::PROVIDER_KEY
            );
        } catch (\Exception $failed) {
            $this->container->get('logger')->error(sprintf(
                'Authentication failed for member "%s" (using password - "%s")',
                $email ?: '(not set)',
                !empty($password) ? 'yes' : 'no'
            ), ['email' => $email]);
            $newException = new AuthenticationCredentialsNotFoundException(
                sprintf('Authentication credentials could not be found for "%s" %', ($email ?: '(not set)'), $password)
            );
            throw $newException;
        }

        try {
            $authenticatedToken = $this->container
                ->get('security.authentication_manager')
                ->authenticate($unauthenticatedToken);

            $this->container->get('security.token_storage')->setToken($authenticatedToken);
        } catch (AuthenticationException $failed) {
            $this->container->get('logger')->error(sprintf(
                'Authentication failed for user "%s" (using password - "%s")',
                $email,
                !empty($password) ? 'yes' : 'no'
            ), ['email' => $email]);
        }

        return $this->container['member'];
    }

    public function isGranted($role)
    {
        if (!$this->container['security.authorization_checker']) {
            return false;
        }

        /** @var AuthorizationChecker $authorizationChecker */
        $authorizationChecker = $this->container['security.authorization_checker'];
        return $authorizationChecker->isGranted($role);
    }

    /**
     * Generate secure random string
     *
     * @param int $nextBytes
     * @param bool $base64encode
     * @return string
     */
    public function secureRandom($nextBytes = 10, $base64encode = true)
    {
        $generator = new SecureRandom();
        $random = $generator->nextBytes($nextBytes);

        return $base64encode ? base64_encode($random) : $random;
    }

    /**
     * Get stored in settings secret
     *
     * @return string
     */
    public function secret()
    {
        return base64_decode($this->container->get('SECRET'));
    }

    /**
     * Generate random token
     *
     * @param int $fromBase
     * @param int $tobase
     * @return string
     */
    public static function random($fromBase = 16, $tobase = 36)
    {
        return base_convert(sha1(uniqid(mt_rand(), true)), $fromBase, $tobase);
    }
}