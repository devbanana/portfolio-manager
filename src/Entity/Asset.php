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
     * @ORM\OneToMany(targetEntity="App\Entity\Holding", mappedBy="asset", orphanRemoval=true)
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
     * @return Collection|Holding[]
     */
    public function getHoldings(): Collection
    {
        return $this->holdings;
    }

    public function addHolding(Holding $holding): self
    {
        if (!$this->holdings->contains($holding)) {
            $this->holdings[] = $holding;
            $holding->setAsset($this);
        }

        return $this;
    }

    public function removeHolding(Holding $holding): self
    {
        if ($this->holdings->contains($holding)) {
            $this->holdings->removeElement($holding);
            // set the owning side to null (unless already changed)
            if ($holding->getAsset() === $this) {
                $holding->setAsset(null);
            }
        }

        return $this;
    }
}
