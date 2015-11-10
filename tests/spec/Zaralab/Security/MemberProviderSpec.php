<?php

namespace spec\Zaralab\Security;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Zaralab\Model\MemberInterface;
use Zaralab\Service\MemberManager;

class MemberProviderSpec extends ObjectBehavior
{
    function let(MemberManager $manager)
    {
        $this->beConstructedWith($manager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Zaralab\Security\MemberProvider');
    }

    function it_should_load_member_by_email($manager)
    {
        $this->loadUserByUsername('email');
        $manager->findMemberByEmail('email')->shouldHaveBeenCalled();
    }

    function it_should_refresh_by_email($manager, MemberInterface $member, MemberInterface $found)
    {
        $member->getId()->shouldBeCalled()->willReturn(1);
        $manager->getClass()->shouldBeCalled()->willReturn('Zaralab\Model\MemberInterface');
        $manager->findMemberById(1)->shouldBeCalled()->willReturn($found);

        $this->refreshUser($member)->shouldReturn($found);
    }

    function it_should_check_if_model_is_supported($manager)
    {
        $manager->getClass()->shouldBeCalled()->willReturn('Zaralab\Model\MemberInterface');

        $this->supportsClass('Zaralab\Model\MemberInterface')->shouldReturn(true);
        $this->supportsClass('Symfony\Component\Security\Core\User\UserInterface')->shouldReturn(false);
    }
}
