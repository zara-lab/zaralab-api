<?php

namespace Zaralab\Entity;

use Zaralab\Model\MemberInterface;

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
     * @var Member
     */
    protected $member;

    public function __construct(MemberInterface $member)
    {
        $this->member = $member;
    }

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

    /**
     * @param $mac
     */
    public function setAddress($mac)
    {
        if (!$this->validateAddress($mac)) {
            throw new \InvalidArgumentException('Invalid mac address.');
        }
        $this->address = $this->normalizeAddress($mac);
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $mac
     * @return int
     */
    protected function validateAddress($mac)
    {
        return preg_match('/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/', $mac);
    }

    /**
     *
     * @param string $mac
     * @return string
     */
    protected function normalizeAddress($mac)
    {
        return strtolower(str_replace(':', '-', $mac));
    }

    /**
     * @return Member|MemberInterface
     */
    public function getMember()
    {
        return $this->member;
    }
}
