<?php

namespace App\Entity;

use App\Repository\SecurityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SecurityRepository::class)
 */
class Security
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $symbol;

    /**
     * @ORM\OneToMany(targetEntity=Fact::class, mappedBy="security")
     */
    private $facts;

    public function __construct()
    {
        $this->facts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
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

    /**
     * @return Collection|Fact[]
     */
    public function getFacts(): Collection
    {
        return $this->facts;
    }

    public function addFact(Fact $fact): self
    {
        if (!$this->facts->contains($fact)) {
            $this->facts[] = $fact;
            $fact->setSecurity($this);
        }

        return $this;
    }

    public function removeFact(Fact $fact): self
    {
        if ($this->facts->removeElement($fact)) {
            // set the owning side to null (unless already changed)
            if ($fact->getSecurity() === $this) {
                $fact->setSecurity(null);
            }
        }

        return $this;
    }
}
