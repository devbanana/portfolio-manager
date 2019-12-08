<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AssetRepository")
 * @ORM\Table(
 *     name="asset",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="symbol_idx", columns={"symbol"})}
 * )
 * @UniqueEntity("symbol")
 */
class Asset
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $symbol;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AssetClass", inversedBy="assets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $assetClass;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isFractional;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=4, nullable=true)
     */
    private $marketPrice;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=8, nullable=true)
     */
    private $dayChange;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=5, nullable=true)
     */
    private $dayChangePercent;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AccountHolding", mappedBy="asset", orphanRemoval=true)
     */
    private $accountHoldings;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PortfolioHolding", mappedBy="asset", orphanRemoval=true)
     */
    private $portfolioHoldings;

    public function __construct()
    {
        $this->accountHoldings = new ArrayCollection();
        $this->portfolioHoldings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): self
    {
        $this->symbol = $symbol;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAssetClass(): ?AssetClass
    {
        return $this->assetClass;
    }

    public function setAssetClass(?AssetClass $assetClass): self
    {
        $this->assetClass = $assetClass;

        return $this;
    }

    public function isFractional(): ?bool
    {
        return $this->isFractional;
    }

    public function setIsFractional(bool $isFractional): self
    {
        $this->isFractional = $isFractional;

        return $this;
    }

    public function getMarketPrice(): ?string
    {
        return $this->marketPrice;
    }

    public function setMarketPrice(?string $marketPrice): self
    {
        $this->marketPrice = $marketPrice;

        return $this;
    }

    public function getDayChange(): ?string
    {
        return $this->dayChange;
    }

    public function setDayChange(?string $dayChange): self
    {
        $this->dayChange = $dayChange;

        return $this;
    }

    public function getDayChangePercent(): ?string
    {
        return $this->dayChangePercent;
    }

    public function setDayChangePercent(?string $dayChangePercent): self
    {
        $this->dayChangePercent = $dayChangePercent;

        return $this;
    }

    /**
     * @return Collection|AccountHolding[]
     */
    public function getAccountHoldings(): Collection
    {
        return $this->accountHoldings;
    }

    public function addAccountHolding(AccountHolding $accountHolding): self
    {
        if (!$this->accountHoldings->contains($accountHolding)) {
            $this->accountHoldings[] = $accountHolding;
            $accountHolding->setAsset($this);
        }

        return $this;
    }

    public function removeAccountHolding(AccountHolding $accountHolding): self
    {
        if ($this->accountHoldings->contains($accountHolding)) {
            $this->accountHoldings->removeElement($accountHolding);
            // set the owning side to null (unless already changed)
            if ($accountHolding->getAsset() === $this) {
                $accountHolding->setAsset(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PortfolioHolding[]
     */
    public function getPortfolioHoldings(): Collection
    {
        return $this->portfolioHoldings;
    }

    public function addPortfolioHolding(PortfolioHolding $portfolioHolding): self
    {
        if (!$this->portfolioHoldings->contains($portfolioHolding)) {
            $this->portfolioHoldings[] = $portfolioHolding;
            $portfolioHolding->setAsset($this);
        }

        return $this;
    }

    public function removePortfolioHolding(PortfolioHolding $portfolioHolding): self
    {
        if ($this->portfolioHoldings->contains($portfolioHolding)) {
            $this->portfolioHoldings->removeElement($portfolioHolding);
            // set the owning side to null (unless already changed)
            if ($portfolioHolding->getAsset() === $this) {
                $portfolioHolding->setAsset(null);
            }
        }

        return $this;
    }
}
