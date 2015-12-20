<?php

namespace Zaralab\Entity;

use Zaralab\Model\MemberInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Annotations;

/**
 * @ORM\Entity
 * @ORM\Table(name="mac_addresses")
 */
class MacAddress
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column("mac_address", type="string", length=17)
     * @var string
     */
    protected $address;

    /**
     * @ORM\ManyToOne(targetEntity="Member", inversedBy="macAddresses", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
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
