<?php

namespace LoginBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity as Unique;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="LoginBundle\Repository\UserRepository")
 * @Unique("username", message="Sorry but this username already exists")
 * @Unique("email", message="Sorry but this email is already taken")
 */
class User implements UserInterface, \Serializable, EquatableInterface 
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=512)
     */
    private $password;

    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="array")
     */
    private $roles;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=64)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=64)
     */
    private $salt;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=64)
     */
    private $surname;

    /**
     * @var date
     *
     * @ORM\Column(name="birthday", type="date", length=64)
     */
    private $birthday;

    /**
     * @var \LoginBundle\Entity\GithubToken
     *
     * @ORM\OneToOne(targetEntity="GithubToken", mappedBy="user")
     */
    private $githubToken;

	/**
	* @var string $googleAuthenticatorCode Stores the secret code
	* @ORM\Column(type="string", length=16, nullable=true)
	*/
	private $googleAuthenticatorCode;


    

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return $this->salt;
    }

    public function eraseCredentials()
    {
        $this->password = null;
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->salt
        ) = unserialize($serialized);
    }

    public function isEqualTo(UserInterface $user)
    {
        return $this->id === $user->getId();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set roles
     *
     * @param array $roles
     *
     * @return User
     */
    public function setRoles($roles)
    {
        if ( is_array( $roles ) )
        {
            $this->roles = $roles;
        } else {
            $this->roles = array($roles);
        }

        return $this;
    }

    /**
     *
     * Add Role
     *
     * @param string
     * 
     * @return Entity
     */
    public function addRole($role)
    {
        if ( !is_array($this->roles))
        {
            $this->roles = array($this->roles);
        }
        if (!in_array($role, $this->roles))
        {
            $this->roles[] = $role;
        }
        return $this;
    }

    /**
     *
     * Remove Role
     *
     * @param string
     * 
     * @return Entity
     */
    public function removeRole ($role)
    {
        if (!in_array($role, $this->roles))
        {
            foreach ($this->roles as $key => $r) 
            {
                if ($r == $role)
                {
                    unset($this->roles[$key]);
                    break;
                }
            }
        }
        return $this;
    }                                                               

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        if (is_array( $this->roles ) )
        {   
            return $this->roles;
        } else {
            return array($this->roles);
        }
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set salt
     *
     * @param string $salt
     *
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set surname
     *
     * @param string $surname
     *
     * @return User
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     *
     * @return User
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set googleAuthenticatorCode
     *
     * @param string $googleAuthenticatorCode
     *
     * @return User
     */
    public function setGoogleAuthenticatorCode($googleAuthenticatorCode)
    {
        $this->googleAuthenticatorCode = $googleAuthenticatorCode;

        return $this;
    }

    /**
     * Get googleAuthenticatorCode
     *
     * @return string
     */
    public function getGoogleAuthenticatorCode()
    {
        return $this->googleAuthenticatorCode;
    }

    /**
     * Set githubToken
     *
     * @param \LoginBundle\Entity\GithubToken $githubToken
     *
     * @return User
     */
    public function setGithubToken(\LoginBundle\Entity\GithubToken $githubToken = null)
    {
        $this->githubToken = $githubToken;

        return $this;
    }

    /**
     * Get githubToken
     *
     * @return \LoginBundle\Entity\GithubToken
     */
    public function getGithubToken()
    {
        return $this->githubToken;
    }
}
