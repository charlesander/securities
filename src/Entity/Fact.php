<?php

namespace App\Entity;

use App\Repository\FactRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FactRepository::class)
 */
class Fact
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Security::class, inversedBy="facts")
     */
    private $security;

    /**
     * @ORM\ManyToOne(targetEntity=Attribute::class, inversedBy="facts")
     */
    private $Attribute;

    /**
     * @ORM\Column(type="integer")
     */
    private $value;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSecurity(): ?Security
    {
        return $this->security;
    }

    public function setSecurity(?Security $security): self
    {
        $this->security = $security;

        return $this;
    }

    public function getAttribute(): ?Attribute
    {
        return $this->Attribute;
    }

    public function setAttribute(?Attribute $Attribute): self
    {
        $this->Attribute = $Attribute;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }
}
