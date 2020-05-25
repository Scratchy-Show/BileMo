<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 *  fields = {"email"},
 *  message = "This email is already in use"
 * )
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "user_detail",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "user_delete",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "list",
 *      href = @Hateoas\Route(
 *          "users_list",
 *          absolute = true
 *      )
 * )
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"list", "detail"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"list", "detail"})
     * @Assert\NotBlank(message = "A first name must be indicated")
     * @Assert\Length(
     *      min = 2,
     *      max = 30,
     *      minMessage = "Your first name must contain at least {{limit}} characters",
     *      maxMessage = "Your first name cannot exceed {{limit}} characters"
     * )
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"list", "detail"})
     * @Assert\NotBlank(message = "A name must be indicated")
     * @Assert\Length(
     *      min = 2,
     *      max = 30,
     *      minMessage = "Your name must contain at least {{limit}} characters",
     *      maxMessage = "Your name cannot be longer than {{limit}} characters"
     * )
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"list", "detail"})
     * @Assert\NotBlank(message = "An email must be indicated")
     * @Assert\Email(message = "The format of the expected email is name@example.com")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"detail"})
     * @Assert\NotBlank(message = "An address must be indicated")
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=20)
     * @Groups({"detail"})
     * @Assert\NotBlank(message = "A zipcode must be indicated")
     * @Assert\Regex(
     *     pattern = "/^\d{5}-\d{4}|\d{5}|[A-Z]\d[A-Z] \d[A-Z]\d$/",
     *     message = "The zipcode is not valid"
     * )
     */
    private $zipcode;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups({"detail"})
     * @Assert\NotBlank(message = "A city must be indicated")
     */
    private $city;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"detail"})
     * @Assert\Type("DateTime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer", inversedBy="customers")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Exclude
     */
    private $customer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }
}
