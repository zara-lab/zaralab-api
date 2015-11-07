<?php

namespace spec\Zaralab\Entity;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MemberSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Zaralab\Entity\Member');
    }

    function it_should_has_id()
    {
        $this->setId(1);
        $this->getId()->shouldReturn(1);
    }

    function it_has_fluent_id_setter()
    {
        $this->setId(1)->shouldReturnAnInstanceOf('Zaralab\Entity\Member');
    }

    function it_should_has_first_name()
    {
        $this->setFirstName('John');
        $this->getFirstName()->shouldReturn('John');
    }

    function it_has_fluent_first_name_setter()
    {
        $this->setFirstName('John')->shouldReturnAnInstanceOf('Zaralab\Entity\Member');
    }

    function it_should_has_last_name()
    {
        $this->setLastName('Doe');
        $this->getLastName()->shouldReturn('Doe');
    }

    function it_has_fluent_last_name_setter()
    {
        $this->setLastName('Doe')->shouldReturnAnInstanceOf('Zaralab\Entity\Member');
    }

    function it_should_concatenate_names()
    {
        $this->setFirstName('John')->setLastName('Doe');
        $this->getNames('Doe')->shouldReturn('John Doe');
    }

    function it_should_has_email()
    {
        $this->setEmail('john.doe@example.com');
        $this->getEmail()->shouldReturn('john.doe@example.com');
    }

    function it_has_fluent_email_setter()
    {
        $this->setEmail('john.doe@example.com')->shouldReturnAnInstanceOf('Zaralab\Entity\Member');
    }

    function it_should_has_phone()
    {
        $this->setPhone('123456789');
        $this->getPhone()->shouldReturn('123456789');
    }

    function it_has_fluent_phone_setter()
    {
        $this->setPhone('123456789')->shouldReturnAnInstanceOf('Zaralab\Entity\Member');
    }

    function it_is_enabled_by_default()
    {
        $this->shouldBeEnabled();
    }

    function it_may_be_disabled()
    {
        $this->setEnabled(false);
        $this->shouldNotBeEnabled();
    }
}
