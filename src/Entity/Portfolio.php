<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PortfolioRepository")
 * @UniqueEntity("name", message="There is already a portfolio by that name.")
 */
class Portfolio
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $cashReserve = false;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=5, nullable=true)
     * @Assert\Positive
     * @Assert\LessThanOrEqual(1, message="Cannot be greater than 100%")
     */
    private $allocationPercent;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PortfolioHolding", mappedBy="portfolio", orphanRemoval=true)
     */
    private $holdings;

    /**
     * @ORM\Column(type="boolean")
     */
    private $unallocated;

    public function __construct()
    {
        $this->holdings = new ArrayCollection();
    }

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

    public function getCashReserve(): ?bool
    {
        return $this->cashReserve;
    }

    public function setCashReserve(bool $cashReserve): self
    {
        $this->cashReserve = $cashReserve;

        return $this;
    }

    public function getAllocationPercent(): ?string
    {
        return $this->allocationPercent;
    }

    public function setAllocationPercent(?string $allocationPercent): self
    {
        $this->allocationPercent = $allocationPercent;

        return $this;
    }

    /**
     * @return Collection|PortfolioHolding[]
     */
    public function getHoldings(): Collection
    {
        return $this->holdings;
    }

    public function addHolding(PortfolioHolding $holding): self
    {
        if (!$this->holdings->contains($holding)) {
            $this->holdings[] = $holding;
            $holding->setPortfolio($this);
        }

        return $this;
    }

    public function removeHolding(PortfolioHolding $holding): self
    {
        if ($this->holdings->contains($holding)) {
            $this->holdings->removeElement($holding);
            // set the owning side to null (unless already changed)
            if ($holding->getPortfolio() === $this) {
                $holding->setPortfolio(null);
            }
        }

        return $this;
    }

    public function getBalance(): string
    {
        $total = '0.00';
        foreach ($this->getHoldings() as $holding) {
            $total = bcadd($total, $holding->getTotalValue(), 2);
        }

        return $total;
    }

    public function getCurrentAllocationPercent(string $total): string
    {
        if (bccomp($total, 0, 4) === 0) {
            // Avoid division by zero
            return '0.0000';
        }
        return bcdiv($this->getBalance(), $total, 4);
    }

    public function getDrift(string $total): string
    {
        return bcsub($this->getCurrentAllocationPercent($total), $this->getAllocationPercent(), 4);
    }

    public function isUnallocated(): ?bool
    {
        return $this->unallocated;
    }

    public function setUnallocated(bool $unallocated): self
    {
        $this->unallocated = $unallocated;

        return $this;
    }

    /**
     * @Assert\Callback
     */
    public function isAllocationPercentRequired(ExecutionContextInterface $context)
    {
        if (!$this->isUnallocated() && empty($this->getAllocationPercent())) {
            $context->buildViolation('Please enter an allocation percent, or check auto-allocate')
                ->atPath('allocationPercent')
                ->addViolation()
            ;
        }
    }
}
