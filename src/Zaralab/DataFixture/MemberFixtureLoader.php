<?php
/**
 * Project: zaralab
 * Filename: MemberFixtureLoader.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 06.11.15
 */

namespace Zaralab\DataFixture;


use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;
use Nelmio\Alice\Persister\Doctrine;

class MemberFixtureLoader extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        /*
        $loader = new \Nelmio\Alice\Fixtures\Loader();
        $objects = $loader->load(__DIR__.'/members.yml');
        */

        $objects = Fixtures::load(__DIR__.'/members.yml', $manager);
        $persister = new Doctrine($manager);
        $persister->persist($objects);
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 1;
    }

}