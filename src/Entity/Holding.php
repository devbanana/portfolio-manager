<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HoldingRepository")
 */
class Holding
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Asset", inversedBy="holdings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $asset;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Account", inversedBy="holdings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $account;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=8)
     */
    private $shares;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $totalValue;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $invested;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAsset(): ?Asset
    {
        return $this->asset;
    }

    public function setAsset(?Asset $asset): self
    {
        $this->asset = $asset;

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getShares(): ?string
    {
        return $this->shares;
    }

    public function setShares(string $shares): self
    {
        $this->shares = $shares;

        return $this;
    }

    public function getTotalValue(): ?string
    {
        return $this->totalValue;
    }

    public function setTotalValue(string $totalValue): self
    {
        $this->totalValue = $totalValue;

        return $this;
    }

    public function getInvested(): ?string
    {
        return $this->invested;
    }

    public function setInvested(string $invested): self
    {
        $this->invested = $invested;

        return $this;
    }
}
