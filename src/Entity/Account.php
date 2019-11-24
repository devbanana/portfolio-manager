<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AccountRepository")
 */
class Account
{
    // Allocation constants.
    const ALLOCATION_COST = 'cost';
    const ALLOCATION_VALUE = 'value';
    const ALLOCATION_TYPES = [self::ALLOCATION_COST, self::ALLOCATION_VALUE];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AccountType")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=5)
     * @Assert\Choice(choices=Account::ALLOCATION_TYPES, message="Choose a valid allocation type")
     */
    private $allocationType = self::ALLOCATION_COST;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?AccountType
    {
        return $this->type;
    }

    public function setType(?AccountType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAllocationType(): ?string
    {
        return $this->allocationType;
    }

    public function setAllocationType(string $allocationType): self
    {
        $this->allocationType = $allocationType;

        return $this;
    }
}
