<?php
/**
 * Project: zaralab
 * Filename: User.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 31.10.15
 */

namespace Zaralab\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Annotations;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\Collection;
use Zaralab\Model\Member as MemberModel;

/**
 * @ORM\Entity
 * @ORM\Table(name="members")
 * @JMS\ExclusionPolicy("none")
 */
class Member extends MemberModel
{


    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(name="first_name", type="string", length=255)
     * @var string
     */
    protected $firstName;

    /**
     * @ORM\Column(name="last_name", type="string", length=255)
     * @var string
     */
    protected $lastName;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @var string
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @JMS\Groups({"admin"})
     * @var string
     */
    protected $phone;

    /**
     * The salt to use for hashing
     *
     * @ORM\Column(type="string")
     * @JMS\Exclude()
     * @var string
     */
    protected $salt;

    /**
     * Encrypted password, persisted.
     *
     * @ORM\Column(type="string")
     * @JMS\Exclude()
     * @var string
     */
    protected $password;

    /**
     * Plain password. Used for model validation. Not persisted.
     *
     * @JMS\Exclude()
     * @var string
     */
    protected $plainPassword;

    /**
     * Not implemented.
     *
     * @JMS\Exclude()
     * @var Collection
     */
    protected $groups;

    /**
     * @ORM\Column(type="array")
     * @JMS\Exclude()
     * @var array
     */
    protected $roles;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Groups({"admin"})
     * @var bool
     */
    protected $enabled;

    /**
     * @var ArrayCollection|MacAddress[]
     */
    protected $macAddresses;

    /**
     * Constructor - member enabled by default when created
     */
    public function __construct()
    {
        $this->enabled = false;
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->roles = array();
        $this->macAddresses = new ArrayCollection();
    }

    /**
     * @return ArrayCollection|MacAddress[]
     */
    public function getMacAddresses()
    {
        return $this->macAddresses;
    }

    /**
     * @param MacAddress $macAddress
     * @return $this
     */
    public function addMacAddress(MacAddress $macAddress)
    {
        if (!$this->macAddresses->contains($macAddress)) {
            $this->macAddresses->add($macAddress);
        }

        return $this;
    }

    /**
     * @param MacAddress $macAddress
     * @return bool
     */
    public function removeMacAddress(MacAddress $macAddress)
    {
        return $this->macAddresses->removeElement($macAddress);
    }

    public function hasMacAddress(MacAddress $macAddress)
    {
        return $this->macAddresses->contains($macAddress);
    }
}