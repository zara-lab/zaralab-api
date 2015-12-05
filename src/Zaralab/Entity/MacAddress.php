<?php

namespace Zaralab\Entity;

class MacAddress
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $address;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function setAddress($mac)
    {
        if (!$this->validateAddress($mac)) {
            throw new \InvalidArgumentException('Invalid mac address.');
        }
        $this->address = $this->normalizeAddress($mac);
    }

    public function getAddress()
    {
        return $this->address;
    }

    protected function validateAddress($mac)
    {
        return preg_match('/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/', $mac);
    }

    protected function normalizeAddress($mac)
    {
        return strtolower(str_replace(':', '-', $mac));
    }
}
