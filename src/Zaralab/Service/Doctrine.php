<?php
/**
 * Project: zaralab
 * Filename: Doctrine.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 31.10.15
 */

namespace Zaralab\Service;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\ApcCache;

class Doctrine
{
    /**
     * @param array $settings
     * @param bool $debug
     * @return EntityManager
     * @throws \Doctrine\ORM\ORMException
     */
    public static function factory($settings, $debug = true)
    {
        if ($debug || !function_exists('apc_fetch')) {
            $cache = new ArrayCache;
        } else {
            $cache = new ApcCache;
        }

        $dbSettings = $settings['doctrine'];

        $config = new Configuration();
        $config->setMetadataCacheImpl($cache);

        // Do not use default Annotation driver
        $driverImpl = new AnnotationDriver(new AnnotationReader(), $dbSettings['entities']);

        // Allow all annotations
        AnnotationRegistry::registerLoader('class_exists');
        $config->setMetadataDriverImpl($driverImpl);
        $config->setQueryCacheImpl($cache);
        $config->setProxyDir($dbSettings['proxy_path']);
        $config->setProxyNamespace('Zaralab\Doctrine\Proxies');

        if ($debug) {
            $config->setAutoGenerateProxyClasses(true);
        } else {
            $config->setAutoGenerateProxyClasses(false);
        }

        $connectionOptions = $dbSettings['dbal'];

        return EntityManager::create($connectionOptions, $config);
    }
}