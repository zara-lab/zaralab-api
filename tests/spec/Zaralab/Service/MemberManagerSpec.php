<?php

namespace spec\Zaralab\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Monolog\Logger;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Zaralab\Model\MemberInterface;

class MemberManagerSpec extends ObjectBehavior
{
    function let(
        EntityManager $em,
        PasswordEncoderInterface $encoder,
        Logger $logger,
        EntityRepository $repo
    ) {
        $this->beConstructedWith($em, $encoder, $logger);
        $em->getRepository('Zaralab\Entity\Member')->willReturn($repo);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Zaralab\Service\MemberManager');
    }

    function it_finds_member_by_id($repo, $logger)
    {
        $repo->findOneBy(['id' => 1], null)->shouldBeCalled();

        $this->findMemberById(1);
        $logger->info(
            "Member table queried (one)",
            [
                "criteria" => ["id" => 1],
                "orderBy"  => null
            ]
        )->shouldHaveBeenCalled();
    }

    function it_finds_active_member_by_id($repo, $logger)
    {
        $repo->findOneBy(['id' => 1, 'enabled' => true], null)->shouldBeCalled();

        $this->findMemberByActive(1);
        $logger->info(
            "Member table queried (one)",
            [
                "criteria" => ["id" => 1, "enabled" => true],
                "orderBy"  => null
            ]
        )->shouldHaveBeenCalled();
    }

    function it_finds_member_by_email($repo, $logger)
    {
        $repo->findOneBy(["email" => "john.doe@example.com"], null)->shouldBeCalled();

        $this->findMemberByEmail('john.doe@example.com');
        $logger->info(
            "Member table queried (one)",
            [
                "criteria" => ["email" => "john.doe@example.com"],
                "orderBy"  => null
            ]
        )->shouldHaveBeenCalled();
    }

    function it_finds_all_members_ordered_by_id($repo, $logger)
    {
        $repo->findBy([], ['id' => 'asc'], null, null)->shouldBeCalled();

        $this->getMembers();
        $logger->info(
            "Member table queried (list)",
            [
                "criteria" => [],
                "orderBy"  => ["id" => "asc"],
                "limit"    => null,
                "offset"   => null
            ]
        )->shouldHaveBeenCalled();
    }

    function it_finds_active_members_ordered_by_first_name_by_default($repo, $logger)
    {
        $repo->findBy(['enabled' => true], ['firstName' => 'asc'], null, null)->shouldBeCalled();

        $this->findMembersByActive();
        $logger->info(
            "Member table queried (list)",
            [
                "criteria" => ["enabled" => true],
                "orderBy"  => ["firstName" => "asc"],
                "limit"    => null,
                "offset"   => null
            ]
        )->shouldHaveBeenCalled();
    }

    function it_is_able_to_find_active_members_ordered_by_other_property($repo, $logger)
    {
        $repo->findBy(['enabled' => true], ['lastName' => 'asc'], null, null)->shouldBeCalled();

        $this->findMembersByActive(['lastName' => 'asc']);
        $logger->info(
            "Member table queried (list)",
            [
                "criteria" => ["enabled" => true],
                "orderBy"  => ["lastName" => "asc"],
                "limit"    => null,
                "offset"   => null
            ]
        )->shouldHaveBeenCalled();
    }

    function it_should_be_able_to_query_with_limit_and_offset($repo, $logger)
    {
        $repo->findBy([], ['id' => 'asc'], 10, 0)->shouldBeCalled();

        $this->findMembersBy([], ['id' => 'asc'], 10, 0);
        $logger->info(
            "Member table queried (list)",
            [
                "criteria" => [],
                "orderBy"  => ['id' => 'asc'],
                "limit"    => 10,
                "offset"   => 0
            ]
        )->shouldHaveBeenCalled();
    }

    function it_should_be_able_to_query_one_with_order($repo, $logger)
    {
        $repo->findOneBy([], ['id' => 'desc'])->shouldBeCalled();

        $this->findMemberBy([], ['id' => 'desc']);
        $logger->info(
            "Member table queried (one)",
            [
                "criteria" => [],
                "orderBy"  => ['id' => 'desc']
            ]
        )->shouldHaveBeenCalled();
    }

    function it_should_be_able_to_reload_member($em, MemberInterface $member)
    {
        $em->refresh($member)->shouldBeCalled();
        $this->reloadMember($member);
    }

    function it_should_update_password_from_plain($encoder, MemberInterface $member)
    {
        $encoder->encodePassword('plain_password', 'salt')->shouldBeCalled()->willReturn('password_encoded');

        $member->getPlainPassword()->shouldBeCalled()->willReturn('plain_password');
        $member->getSalt()->shouldBeCalled()->willReturn('salt');
        $member->setPassword('password_encoded')->shouldBeCalled();
        $member->eraseCredentials()->shouldBeCalled();

        $this->updatePassword($member);
    }

    function it_should_update_user_and_flush($em, MemberInterface $member)
    {
        $em->persist($member)->shouldBeCalled();
        $em->flush()->shouldBeCalled();

        $this->updateMember($member);
    }

    function it_should_update_user_without_flush($em, MemberInterface $member)
    {
        $em->persist($member)->shouldBeCalled();
        $em->flush()->shouldNotBeCalled();

        $this->updateMember($member, false);
    }

    function it_should_delete_user_and_flush($em, MemberInterface $member)
    {
        $em->remove($member)->shouldBeCalled();
        $em->flush()->shouldBeCalled();

        $this->deleteMember($member);
    }

    function it_should_create_member_object()
    {
        $this->createMember()->shouldReturnAnInstanceOf('Zaralab\Model\MemberInterface');
    }

    function it_should_know_member_entitiy_class()
    {
        $this->getClass()->shouldReturn('Zaralab\Entity\Member');
    }
}
