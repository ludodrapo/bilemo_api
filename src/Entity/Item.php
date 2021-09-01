<?php

namespace App\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use App\Repository\ItemRepository;

/**
 * Class Item
 * @package App\Entity
 * @ORM\Entity(repositoryClass=ItemRepository::class)
 */
class Item
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"list", "show"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"list", "show"})
     * @var string|null
     */
    private ?string $brand = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"list", "show"})
     * @var string|null
     */
    private ?string $model = null;

    /**
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"list", "show"})
     * @var integer|null
     */
    private ?int $price = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"show"})
     * @var string|null
     */
    private ?string $screenSize = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Serializer\Groups({"show"})
     */
    private ?int $internalMemory = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Groups({"show"})
     * @var string|null
     */
    private ?string $color = null;

    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Groups({"show"})
     * @var boolean|null
     */
    private ?bool $waterResistant = false;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Serializer\Groups({"show"})
     * @var \DateTimeImmutable|null
     */
    private ?\DateTimeInterface $createdAt = null;

    /**
     * Item constructor.
     */
    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }

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
    public function getBrand(): ?string
    {
        return $this->brand;
    }

    /**
     * @param string $brand
     * @return self
     */
    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getModel(): ?string
    {
        return $this->model;
    }

    /**
     * @param string $model
     * @return self
     */
    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return integer|null
     */
    public function getPrice(): ?int
    {
        return $this->price;
    }

    /**
     * @param integer $price
     * @return self
     */
    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getScreenSize(): ?string
    {
        return $this->screenSize;
    }

    /**
     * @param string $screenSize
     * @return self
     */
    public function setScreenSize(string $screenSize): self
    {
        $this->screenSize = $screenSize;

        return $this;
    }

    /**
     * @return integer|null
     */
    public function getInternalMemory(): ?int
    {
        return $this->internalMemory;
    }

    /**
     * @param integer|null $internalMemory
     * @return self
     */
    public function setInternalMemory(?int $internalMemory): self
    {
        $this->internalMemory = $internalMemory;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return boolean|null
     */
    public function getWaterResistant(): ?bool
    {
        return $this->waterResistant;
    }

    /**
     * @param boolean $waterResistant
     * @return self
     */
    public function setWaterResistant(bool $waterResistant): self
    {
        $this->waterResistant = $waterResistant;

        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeImmutable $createdAt
     * @return self
     */
    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
