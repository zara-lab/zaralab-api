<?php

namespace spec\Zaralab\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Zaralab\Model\GroupInterface;
use Zaralab\Model\MemberInterface;
use Zaralab\Spec\Matcher as Match;

class MemberSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Zaralab\Model\MemberInterface');
    }

    function it_should_has_id()
    {
        $this->setId(1);
        $this->getId()->shouldReturn(1);
    }

    function it_has_fluent_id_setter()
    {
        $this->setId(1)->shouldReturnAnInstanceOf('Zaralab\Model\MemberInterface');
    }

    function it_should_has_first_name()
    {
        $this->setFirstName('John');
        $this->getFirstName()->shouldReturn('John');
    }

    function it_has_fluent_first_name_setter()
    {
        $this->setFirstName('John')->shouldReturnAnInstanceOf('Zaralab\Model\MemberInterface');
    }

    function it_should_has_last_name()
    {
        $this->setLastName('Doe');
        $this->getLastName()->shouldReturn('Doe');
    }

    function it_has_fluent_last_name_setter()
    {
        $this->setLastName('Doe')->shouldReturnAnInstanceOf('Zaralab\Model\MemberInterface');
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
        $this->setEmail('john.doe@example.com')->shouldReturnAnInstanceOf('Zaralab\Model\MemberInterface');
    }

    function it_should_has_phone()
    {
        $this->setPhone('123456789');
        $this->getPhone()->shouldReturn('123456789');
    }

    function it_has_fluent_phone_setter()
    {
        $this->setPhone('123456789')->shouldReturnAnInstanceOf('Zaralab\Model\MemberInterface');
    }

    function it_is_not_enabled_by_default()
    {
        $this->shouldNotBeEnabled();
    }

    function it_may_be_enabled()
    {
        $this->setEnabled(true);
        $this->shouldBeEnabled();
    }

    function it_should_generate_salt_on_init()
    {
        $this->getSalt()->shouldNotBeEmptyString();
    }

    function it_should_have_fluent_salt_setter()
    {
        $this->setSalt('salt')->shouldReturnAnInstanceOf('Zaralab\Model\MemberInterface');
        $this->getSalt()->shouldReturn('salt');
    }

    function it_should_have_encoded_password_and_fluent_password_setter()
    {
        $this->setPassword('encoded')->shouldReturnAnInstanceOf('Zaralab\Model\MemberInterface');
        $this->getPassword()->shouldReturn('encoded');
    }

    function it_should_be_able_to_set_plain_password_with_fluent_setter()
    {
        $this->setPlainPassword('plain')->shouldReturnAnInstanceOf('Zaralab\Model\MemberInterface');
        $this->getPlainPassword()->shouldReturn('plain');
    }

    function it_should_be_able_to_erase_sensitive_data()
    {
        $this->setPlainPassword('plain');
        $this->eraseCredentials()->shouldReturnAnInstanceOf('Zaralab\Model\MemberInterface');
        $this->getPlainPassword()->shouldReturn(null);
    }

    function it_should_have_empty_group_collection_by_default(GroupInterface $group)
    {
        $group->getName()->willReturn('group');

        $this->getGroups()->shouldReturnAnInstanceOf('Doctrine\Common\Collections\ArrayCollection');
        $this->getGroups()->shouldHaveCount(0);
    }

    function it_should_be_able_to_add_group(GroupInterface $group)
    {
        $group->getName()->willReturn('group');
        $this->addGroup($group);
        $this->getGroups()->shouldHaveCount(1);
        $this->shouldHaveGroup('group');
    }

    function it_should_have_fluent_add_group_setter(GroupInterface $group)
    {
        $this->addGroup($group)->shouldReturnAnInstanceOf('Zaralab\Model\MemberInterface');
    }

    function it_should_not_duplicate_groups(GroupInterface $group)
    {
        $group->getName()->willReturn('group');

        $this->addGroup($group);
        $this->addGroup($group);
        $this->getGroups()->shouldHaveCount(1);
    }

    function it_should_return_group_names(GroupInterface $group)
    {
        $group->getName()->willReturn('group');

        $this->addGroup($group);
        $this->getGroupNames()->shouldReturn(['group']);
    }

    function it_should_be_able_to_remove_group(GroupInterface $group, ArrayCollection $collection)
    {
        $group->getName()->willReturn('group');

        $this->addGroup($group);
        $this->removeGroup($group);
        $this->getGroups()->shouldHaveCount(0);
    }

    function it_should_have_fluent_remove_group_method(GroupInterface $group)
    {
        $this->removeGroup($group)->shouldReturnAnInstanceOf('Zaralab\Model\MemberInterface');
    }

    function it_should_be_able_to_reset_groups(ArrayCollection $collection)
    {
        $collection->count()->willReturn(1);
        $this->setGroups($collection);
        $this->getGroups()->shouldHaveCount(1);
    }

    function it_should_have_fluent_group_setter(ArrayCollection $collection)
    {
        $this->setGroups($collection)->shouldReturnAnInstanceOf('Zaralab\Model\MemberInterface');
    }

    function it_should_have_default_role()
    {
        $this->getRoles()->shouldReturn([MemberInterface::ROLE_USER]);
    }

    function it_should_merge_with_group_roles(GroupInterface $group)
    {
        $group->getName()->willReturn('group');
        $group->getRoles()->willReturn([MemberInterface::ROLE_ADMIN]);

        $this->addGroup($group);
        $this->getRoles()->shouldHaveCount(2);
    }

    function it_should_be_able_to_add_role()
    {
        $this->addRole(MemberInterface::ROLE_ADMIN);
        $this->getRoles()->shouldHaveCount(2);
        $this->shouldHaveRole(MemberInterface::ROLE_ADMIN);
    }

    function it_should_not_duplicate_roles_while_adding()
    {
        $this->addRole(MemberInterface::ROLE_ADMIN);
        $this->addRole(MemberInterface::ROLE_ADMIN);
        $this->getRoles()->shouldHaveCount(2);
    }

    function it_should_have_fluent_add_role_setter()
    {
        $this->addRole(MemberInterface::ROLE_ADMIN)->shouldReturnAnInstanceOf('Zaralab\Model\MemberInterface');
    }

    function it_should_be_able_to_set_roles()
    {
        $this->setRoles([MemberInterface::ROLE_ADMIN, MemberInterface::ROLE_USER]);
        $this->getRoles()->shouldHaveCount(2);
        $this->shouldHaveRole(MemberInterface::ROLE_ADMIN);
    }

    function it_should_not_duplicate_roles_while_setting()
    {
        $this->setRoles([MemberInterface::ROLE_ADMIN, MemberInterface::ROLE_ADMIN, MemberInterface::ROLE_USER]);
        $this->getRoles()->shouldHaveCount(2);
    }

    function it_should_have_fluent_role_setter()
    {
        $this->setRoles([MemberInterface::ROLE_ADMIN])->shouldReturnAnInstanceOf('Zaralab\Model\MemberInterface');
    }

    function it_should_be_able_to_remove_roles()
    {
        $this->setRoles([MemberInterface::ROLE_ADMIN, MemberInterface::ROLE_USER]);
        $this->removeRole(MemberInterface::ROLE_ADMIN);
        $this->shouldNotHaveRole(MemberInterface::ROLE_ADMIN);
    }

    function it_should_have_fluent_role_remove_method()
    {
        $this->removeRole(MemberInterface::ROLE_ADMIN)->shouldReturnAnInstanceOf('Zaralab\Model\MemberInterface');
    }

    function it_should_not_lose_default_role()
    {
        $this->setRoles([]);
        $this->removeRole(MemberInterface::ROLE_DEFAULT);
        $this->shouldHaveRole(MemberInterface::ROLE_DEFAULT);
    }

    function it_should_has_super_admin_manage_shortcut()
    {
        $this->setSuperAdmin(true);
        $this->shouldHaveRole(MemberInterface::ROLE_SUPER_ADMIN);
        $this->shouldBeSuperAdmin();
        $this->setSuperAdmin(false);
        $this->shouldNotHaveRole(MemberInterface::ROLE_SUPER_ADMIN);
        $this->shouldNotBeSuperAdmin();
    }

    function it_is_serializable()
    {
        $serialized = serialize(array(
            'password',
            'salt',
            true,
            1,
            'email',
        ));

        $this->unserialize($serialized);
        $this->getEmail()->shouldBe('email');
        $this->getPassword()->shouldBe('password');
        $this->getSalt()->shouldBe('salt');
        $this->getId()->shouldBe(1);
        $this->shouldBeEnabled();

        $this->serialize()->shouldReturn($serialized);
    }

    function it_should_have_account_non_expired_by_default()
    {
        $this->shouldBeAccountNonExpired();
    }

    function it_is_non_locked_by_default()
    {
        $this->isAccountNonLocked();
    }

    function it_should_have_credentials_non_expired_by_default()
    {
        $this->isCredentialsNonExpired();
    }

    function it_should_convert_to_email_string()
    {
        $this->setEmail('email');
        $this->__toString()->shouldBe('email');
    }

    function getMatchers()
    {
        return [
            'beEmptyString'     => new Match\StringEmpty(),
        ];
    }
}
