<?php

namespace spec\Zaralab\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Monolog\Logger;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MemberManagerSpec extends ObjectBehavior
{
    function let(EntityManager $em, Logger $logger, EntityRepository $repo)
    {
        $this->beConstructedWith($em, $logger);
        $em->getRepository('Zaralab\Entity\Member')->willReturn($repo);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Zaralab\Service\MemberManager');
    }

    function it_returns_member_by_id($repo, $logger)
    {
        $repo->findOneBy(['id' => 1], null)->shouldBeCalled();

        $this->get(1);
        $logger->info("Member table queried (one)", [
            "criteria" => ["id" => 1],
            "orderBy" => null
        ])->shouldHaveBeenCalled();
    }

    function it_throws_exception_on_missing_id($repo, $logger)
    {
        $this->shouldThrow('\InvalidArgumentException')->duringGet('bad');
        $this->shouldThrow('\InvalidArgumentException')->duringGetByActive('bad');
    }

    function it_returns_active_member_by_id($repo, $logger)
    {
        $repo->findOneBy(['id' => 1, 'enabled' => true], null)->shouldBeCalled();

        $this->getByActive(1);
        $logger->info("Member table queried (one)", [
            "criteria" => ["id" => 1, "enabled" => true],
            "orderBy" => null
        ])->shouldHaveBeenCalled();
    }

    function it_returns_all_members_ordered_by_id($repo, $logger)
    {
        $repo->findBy([], ['id' => 'asc'], null, null)->shouldBeCalled();

        $this->getAll();
        $logger->info("Member table queried (list)", [
            "criteria" => [],
            "orderBy" => ["id" => "asc"],
            "limit" => null,
            "offset" => null
        ])->shouldHaveBeenCalled();
    }

    function it_returns_active_members_ordered_by_first_name_by_default($repo, $logger)
    {
        $repo->findBy(['enabled' => true], ['firstName' => 'asc'], null, null)->shouldBeCalled();

        $this->getAllByActive();
        $logger->info("Member table queried (list)", [
            "criteria" => ["enabled" => true],
            "orderBy" => ["firstName" => "asc"],
            "limit" => null,
            "offset" => null
        ])->shouldHaveBeenCalled();
    }

    function it_is_able_to_return_active_members_ordered_by_other_property($repo, $logger)
    {
        $repo->findBy(['enabled' => true], ['lastName' => 'asc'], null, null)->shouldBeCalled();

        $this->getAllByActive(['lastName' => 'asc']);
        $logger->info("Member table queried (list)", [
            "criteria" => ["enabled" => true],
            "orderBy" => ["lastName" => "asc"],
            "limit" => null,
            "offset" => null
        ])->shouldHaveBeenCalled();
    }

    function it_should_be_able_to_query_with_limit_and_offset($repo, $logger)
    {
        $repo->findBy([], ['id' => 'asc'], 10, 0)->shouldBeCalled();

        $this->getListBy([], ['id' => 'asc'], 10, 0);
        $logger->info("Member table queried (list)", [
            "criteria" => [],
            "orderBy" => ['id' => 'asc'],
            "limit" => 10,
            "offset" => 0
        ])->shouldHaveBeenCalled();
    }

    function it_should_be_able_to_query_one_with_order($repo, $logger)
    {
        $repo->findOneBy([], ['id' => 'desc'])->shouldBeCalled();

        $this->getOneBy([], ['id' => 'desc']);
        $logger->info("Member table queried (one)", [
            "criteria" => [],
            "orderBy" => ['id' => 'desc']
        ])->shouldHaveBeenCalled();
    }
}
