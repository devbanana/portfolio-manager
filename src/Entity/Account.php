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
     * The type of allocation.
     *
     * Accounts can have their assets allocated by either cost or value.
     *
     * Cost means that the allocation percent is based on the amount actually
     * invested in that asset.
     *
     * Value means that the allocation percent looks instead at
     * the current total of the asset vs. the total value of the account.
     *
     * For example, imagine you invest $500 each in Stock A and Stock B,
     * setting a 50% allocation for both. Then stock A rises to $600 and stock B rises to $700.
     * By a cost allocation, they are still at 50% because you only invested $500 in each.
     * But by a value allocation, Stock A would be at 46.15% (600/[600+700]),
     * and Stock B would have an allocation of 53.85% (700/[600+700])
     *
     * @ORM\Column(type="string", length=5)
     * @Assert\Choice(choices=Account::ALLOCATION_TYPES, message="Choose a valid allocation type")
     */
    private $allocationType = self::ALLOCATION_COST;

    /**
     * Allocation of this account in comparison to the entire portfolio.
     *
     * For example, if you set an allocation percent of 50%, then this account will make up
     * 50% of your total portfolio.
     *
     * @ORM\Column(type="decimal", precision=6, scale=5)
     * @Assert\Range(
     *     min = 0.0,
     *     max = 1.0,
     *     notInRangeMessage = "Allocation must be between 0% and 100%.",
     *     maxMessage = "Percent cannot be over 100%"
     * )
     */
    private $allocationPercent = 0.00;

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

    public function getAllocationPercent(): ?string
    {
        return $this->allocationPercent;
    }

    public function setAllocationPercent(string $allocationPercent): self
    {
        $this->allocationPercent = $allocationPercent;

        return $this;
    }
}
