<?php

namespace App\Entity;

use App\Entity\Client;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Hateoas\Configuration\Annotation as Hateoas;


/**
 * Class User
 * @package App\Entity
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 *     fields={"email"}, 
 *     message="This email already exists."
 * )
 * @Serializer\ExclusionPolicy("ALL")
 * @Hateoas\Relation(
 *     "self",
 *     href = @Hateoas\Route(
 *         "api_users_show_one",
 *         parameters = { "id" = "expr(object.getId())" },
 *         absolute = true
 *     )
 * )
 * @Hateoas\Relation(
 *     "delete",
 *     href = @Hateoas\Route(
 *         "api_users_delete_one",
 *         parameters = { "id" = "expr(object.getId())" },
 *         absolute = true
 *     )
 * )
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Expose
     * @var integer|null
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *     min=3,
     *     minMessage="The user's name must contain at least {{ limit }} characters."
     * )
     * @var string|null
     * @Serializer\Expose
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="The user's email must not be empty."
     * )
     * @Assert\Email(
     *     message="The provided email address is not valid."
     * )
     * @var string|null
     * @Serializer\Expose
     */
    private ?string $email = null;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @var \DateTimeInterface|null
     */
    private ?\DateTimeInterface $createdAt = null;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @var Client
     */
    private $client;

    /**
     * @return integer|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return self
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeImmutable $createdAt
     * @return self
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Client|null
     */
    public function getClient(): ?Client
    {
        return $this->client;
    }

    /**
     * @param Client|null $client
     * @return self
     */
    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }
}
