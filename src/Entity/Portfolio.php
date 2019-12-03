<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PortfolioRepository")
 * @ORM\HasLifecycleCallbacks()
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
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $cashReserve;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=5, nullable=true)
     */
    private $allocationPercent;
    private $calculatedAllocation;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PortfolioHolding", mappedBy="portfolio", orphanRemoval=true)
     */
    private $holdings;

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

    public function getCalculatedAllocationPercent(): string
    {
        if ($this->getAllocationPercent() === null) {
            return $this->calculatedAllocation;
        }

        return $this->getAllocationPercent();
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
        return bcsub($this->getCurrentAllocationPercent($total), $this->getCalculatedAllocationPercent(), 4);
    }

    /**
     * @ORM\PostLoad()
     */
    public function calculateAllocationPercent(LifecycleEventArgs $args)
    {
        if ($this->getAllocationPercent() !== null) {
            return;
        }

        $result = $args->getEntityManager()->createQueryBuilder()
            ->select('sum(p.allocationPercent) as percent')
            ->addSelect('count(p.allocationPercent) as total')
            ->from('App\Entity\Portfolio', 'p')
            ->getQuery()
            ->getSingleResult()
        ;

        $percent = $result['percent'];
        $total = $result['total'];

        $this->calculatedAllocation = bcdiv(bcsub(1, $percent, 4), $total, 4);
    }
}
