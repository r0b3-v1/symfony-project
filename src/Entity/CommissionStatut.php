<?php

namespace App\Entity;

use App\Repository\CommissionStatutRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommissionStatutRepository::class)
 */
class CommissionStatut
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Commission::class, mappedBy="statut")
     */
    private $commissions;

    public function __construct()
    {
        $this->commissions = new ArrayCollection();
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
     * @return Collection<int, Commission>
     */
    public function getCommissions(): Collection
    {
        return $this->commissions;
    }

    public function addCommission(Commission $commission): self
    {
        if (!$this->commissions->contains($commission)) {
            $this->commissions[] = $commission;
            $commission->setStatut($this);
        }

        return $this;
    }

    public function removeCommission(Commission $commission): self
    {
        if ($this->commissions->removeElement($commission)) {
            // set the owning side to null (unless already changed)
            if ($commission->getStatut() === $this) {
                $commission->setStatut(null);
            }
        }

        return $this;
    }
}
