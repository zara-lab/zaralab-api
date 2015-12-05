<?php

namespace spec\Zaralab\Entity;

use PhpSpec\ObjectBehavior;
use Zaralab\Entity\MacAddress;

class OnlineMacAddressSpec extends ObjectBehavior
{
    function let(MacAddress $macAddress)
    {
        $this->beConstructedWith($macAddress);
    }
    
    function it_is_initializable()
    {
        $this->shouldHaveType('Zaralab\Entity\OnlineMacAddress');
    }
    
    function it_should_have_macaddress(MacAddress $macaddress) 
    {
        $this->getMacAddress()->shouldHaveType('Zaralab\Entity\MacAddress');
    }
    
}
