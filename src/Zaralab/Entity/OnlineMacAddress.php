<?php

namespace Zaralab\Entity;

/**
 * Online mac addresses.
 */
class OnlineMacAddress
{   
    /**
     *
     * @var MacAddress
     */
    protected $macAddress;
    
    /**
     * 
     * @param MacAddress $macAddress
     */
    public function __construct(MacAddress $macAddress)
    {
        $this->macAddress = $macAddress;
    }
    
    /**
     * 
     * @return MacAddress
     */
    function getMacAddress() 
    {
        return $this->macAddress;
    }
    
}
