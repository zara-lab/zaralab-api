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
use Interop\Container\ContainerInterface;
use Nelmio\Alice\Fixtures;
use Nelmio\Alice\Persister\Doctrine;
use Nelmio\Alice\Fixtures\Loader;
use Zaralab\Framework\Di\ContainerAwareInterface;


class MemberFixtureLoader extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $om
     */
    public function load(ObjectManager $om)
    {
        // auto save to db example
        // $members = Fixtures::load(__DIR__.'/members.yml', $om);

        $loader = new Loader();
        $members = $loader->load(__DIR__.'/members.yml');


        $manager = $this->container->get('member.manager');


        foreach ($members as $member) {
            $manager->updateMember($member, false);
        }

        $persister = new Doctrine($om);
        $persister->persist($members);
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

    /**
     * @inheritDoc
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

}