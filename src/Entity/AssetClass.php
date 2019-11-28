<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AssetClassRepository")
 */
class AssetClass
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
     * @ORM\OneToMany(targetEntity="App\Entity\Asset", mappedBy="assetClass")
     */
    private $assets;

    public function __construct()
    {
        $this->assets = new ArrayCollection();
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

    /**
     * @return Collection|Asset[]
     */
    public function getAssets(): Collection
    {
        return $this->assets;
    }

    public function addAsset(Asset $asset): self
    {
        if (!$this->assets->contains($asset)) {
            $this->assets[] = $asset;
            $asset->setAssetClass($this);
        }

        return $this;
    }

    public function removeAsset(Asset $asset): self
    {
        if ($this->assets->contains($asset)) {
            $this->assets->removeElement($asset);
            // set the owning side to null (unless already changed)
            if ($asset->getAssetClass() === $this) {
                $asset->setAssetClass(null);
            }
        }

        return $this;
    }
}
