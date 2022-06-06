<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\TagRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=TagRepository::class)
 */
class Tag {
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
     * @ORM\ManyToMany(targetEntity=Submission::class, mappedBy="tags")
     */
    private $submissions;

    public function __construct() {
        $this->submissions = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Submission>
     */
    public function getSubmissions(): Collection {
        return $this->submissions;
    }

    public function addSubmission(Submission $submission): self {
        if (!$this->submissions->contains($submission)) {
            $this->submissions[] = $submission;
            $submission->addTag($this);
        }

        return $this;
    }

    public function removeSubmission(Submission $submission): self {
        if ($this->submissions->removeElement($submission)) {
            $submission->removeTag($this);
        }

        return $this;
    }
}
