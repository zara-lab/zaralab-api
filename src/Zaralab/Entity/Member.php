<?php
/**
 * Project: zaralab
 * Filename: User.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 31.10.15
 */

namespace Zaralab\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Annotations;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity
 * @ORM\Table(name="members")
 * @JMS\ExclusionPolicy("none")
 */
class Member
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
     * @ORM\Column(type="boolean")
     * @JMS\Groups({"admin"})
     * @var bool
     */
    protected $enabled;

    /**
     * Constructor - member enabled by default when created
     */
    public function __construct()
    {
        $this->enabled = true;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Member
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return Member
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return Member
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getNames()
    {
        return $this->getFirstName().' '.$this->getLastName();
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Member
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     * @return Member
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param boolean $enabled
     * @return Member
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }
}