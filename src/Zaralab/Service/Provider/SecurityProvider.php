<?php
/**
 * Project: zaralab
 * Filename: SecurityProvider.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 10.11.15
 */

namespace Zaralab\Service\Provider;


use Interop\Container\ContainerInterface;
use Pimple\ServiceProviderInterface;
use Pimple\Container;
use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolver;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Core\User\UserChecker;
use Zaralab\Security\MemberProvider;
use Zaralab\Service\SecurityManager;

/**
 * Integrate the required symfony2 security services.
 */
class SecurityProvider implements ServiceProviderInterface
{
    /**
     * Settings section will be added once
     * any configuration is required.
     *
     * @inheritDoc
     */
    public function register(Container $container)
    {
        $container['security.encoder'] = function ($container) {
            return new MessageDigestPasswordEncoder();
        };

        $container['security.role_hierarchy'] = [
            'ROLE_SUPER_ADMIN' => array('ROLE_ADMIN', 'ROLE_USER'),
        ];

        $container['security.manager'] = function ($container) {
            return new SecurityManager($container);
        };

        $container['security.token_storage'] = function ($container) {
            return new TokenStorage();
        };

        $container['security.user_checker'] = function ($container) {
            return new UserChecker();
        };

        $container['member'] = $container->factory(function ($app) {
            if (null === $token = $app['security.token_storage']->getToken()) {
                return null;
            }
            if (!is_object($user = $token->getUser())) {
                return null;
            }
            return $user;
        });
    }
}