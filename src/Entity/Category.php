<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'category')]
    private Collection $price;

    #[ORM\Column(length: 255)]
    private ?string $icon = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->price = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getPrice(): Collection
    {
        return $this->price;
    }

    public function addPrice(Product $price): static
    {
        if (!$this->price->contains($price)) {
            $this->price->add($price);
            $price->setCategory($this);
        }

        return $this;
    }

    public function removePrice(Product $price): static
    {
        if ($this->price->removeElement($price)) {
            // set the owning side to null (unless already changed)
            if ($price->getCategory() === $this) {
                $price->setCategory(null);
            }
        }

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): static
    {
        $this->icon = $icon ? trim($icon) : null;
        return $this;
    }
}
