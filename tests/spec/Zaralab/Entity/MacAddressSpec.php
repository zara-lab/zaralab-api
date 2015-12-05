<?php

namespace spec\Zaralab\Entity;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Zaralab\Model\MemberInterface;

class MacAddressSpec extends ObjectBehavior
{
    function let(MemberInterface $member)
    {
        $this->beConstructedWith($member);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Zaralab\Entity\MacAddress');
    }

    function it_should_have_id()
    {
        $this->setId(1);
        $this->getId()->shouldReturn(1);
    }

    function it_should_have_mac_address_field()
    {
        $this->setAddress('3d-f2-c9-a6-b3-4f');
        $this->getAddress()->shouldReturn('3d-f2-c9-a6-b3-4f');
    }

    function it_should_validate_mac_address()
    {
        $this->shouldThrow('\InvalidArgumentException')->duringSetAddress('invalid');
    }

    function it_should_normalize_mac_address()
    {
        $this->setAddress('3D-F2-C9-A6-B3-4F');
        $this->getAddress()->shouldReturn('3d-f2-c9-a6-b3-4f');

        $this->setAddress('3D:F2:C9:A6:B3:4F');
        $this->getAddress()->shouldReturn('3d-f2-c9-a6-b3-4f');
    }

    function it_should_have_member(MemberInterface $member)
    {
        $this->getMember()->shouldReturn($member);
    }

}
