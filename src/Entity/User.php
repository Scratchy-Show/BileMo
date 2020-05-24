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
 *  message = "Cet email est déjà utilisé"
 * )
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "show_user",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "delete_user",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "list",
 *      href = @Hateoas\Route(
 *          "list_users",
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
     * @Groups({"list", "showUser"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"list", "showUser"})
     * @Assert\Length(
     *      min = 2,
     *      max = 30,
     *      minMessage = "Votre prénom doit contenir au moins {{ limit }} caractères",
     *      maxMessage = "Votre prénom ne peut doit pas dépasser les {{ limit }} caractères"
     * )
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"list", "showUser"})
     * @Assert\NotBlank(message = "Un nom doit être indiqué")
     * @Assert\Length(
     *      min = 2,
     *      max = 30,
     *      minMessage = "Votre nom doit contenir au moins {{ limit }} caractères",
     *      maxMessage = "Votre nom ne peut doit pas dépasser les {{ limit }} caractères"
     * )
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"list", "showUser"})
     * @Assert\NotBlank(message = "Un email doit être indiqué")
     * @Assert\Email(message = "Le format de l'email attendu est nom@exemple.fr")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"showUser"})
     * @Assert\NotBlank(message = "Une adresse doit être indiqué")
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=20)
     * @Groups({"showUser"})
     * @Assert\NotBlank(message = "Un code postale doit être indiqué")
     * @Assert\Regex(
     *     pattern = "/^\d{5}-\d{4}|\d{5}|[A-Z]\d[A-Z] \d[A-Z]\d$/",
     *     message = "Le code postale n'est pas valide"
     * )
     */
    private $zipcode;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups({"showUser"})
     * @Assert\NotBlank(message = "Une ville doit être indiqué")
     */
    private $city;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"showUser"})
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
