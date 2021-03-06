<?php

namespace App\Entity;

use App\Repository\ProgramRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;

//Ici on importe le package Vich, que l’on utilisera sous l’alias “Vich”
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=ProgramRepository::class)
 * @Assert\EnableAutoMapping()
 * @UniqueEntity(fields={"title"}, message="Cette série existe déjà.")
 * //On précise à l’entité que nous utiliserons l’upload du package Vich uploader
 * @Vich\Uploadable
 */
class Program
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner un titre.")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Veuillez renseigner le synopsis de la série.")
     * @Assert\Regex(
     *      pattern="/plus belle la vie/",
     *      match=false,
     *      message="On parle de vraies séries ici !"
     * )
     */
    private $summary;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $poster;

    //On va créer un nouvel attribut à notre entité, qui ne sera pas lié à une colonne
    //Tu peux d’ailleurs voir que l’annotation ORM column n’est pas spécifiée car
    //On ne rajoute pas de données de type file en bdd
    /**
     * @Vich\UploadableField(mapping="poster_file", fileNameProperty="poster")
     * @Assert\File(
     *      maxSize = "1M",
     *      mimeTypes = {"image/jpeg", "image/png", "image/webp"},
     * )
     * @var File
     */
      private $posterFile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="programs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=Season::class, mappedBy="program", orphanRemoval=true)
     */
    private $seasons;

    /**
     * @ORM\ManyToMany(targetEntity=Actor::class, mappedBy="programs")
     */
    private $actors;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="programs")
     */
    private $owner;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="watchlist")
     */
    private $viewers;

    public function __construct()
    {
        $this->seasons = new ArrayCollection();
        $this->actors = new ArrayCollection();
        $this->viewers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getPoster(): ?string
    {
        return $this->poster;
    }

    public function setPoster(?string $poster): self
    {
        $this->poster = $poster;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Season[]
     */
    public function getSeasons(): Collection
    {
        return $this->seasons;
    }

    public function addSeason(Season $season): self
    {
        if (!$this->seasons->contains($season)) {
            $this->seasons[] = $season;
            $season->setProgram($this);
        }

        return $this;
    }

    public function removeSeason(Season $season): self
    {
        if ($this->seasons->removeElement($season)) {
            // set the owning side to null (unless already changed)
            if ($season->getProgram() === $this) {
                $season->setProgram(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Actor[]
     */
    public function getActors(): Collection
    {
        return $this->actors;
    }

    public function addActor(Actor $actor): self
    {
        if (!$this->actors->contains($actor)) {
            $this->actors[] = $actor;
            $actor->addProgram($this);
        }

        return $this;
    }

    public function removeActor(Actor $actor): self
    {
        if ($this->actors->removeElement($actor)) {
            $actor->removeProgram($this);
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getViewers(): Collection
    {
        return $this->viewers;
    }

    public function addViewer(User $viewer): self
    {
        if (!$this->viewers->contains($viewer)) {
            $this->viewers[] = $viewer;
            $viewer->addWatchlist($this);
        }

        return $this;
    }

    public function removeViewer(User $viewer): self
    {
        if ($this->viewers->removeElement($viewer)) {
            $viewer->removeWatchlist($this);
        }

        return $this;
    }

    public function setPosterFile(File $image = null):Program
    {
        $this->posterFile = $image;
        if ($image){
            $this->updatedAt = new \DateTime('now');
        }
        
        return $this;
    }

    public function getPosterFile(): ?File
    {
        return $this->posterFile;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function __toString()
    {
        return $this->getTitle();
    }
}
