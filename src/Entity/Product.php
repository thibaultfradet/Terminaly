<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Category $category = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 7, scale: 2)]
    private ?float $price = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'salesProducts')]
    private ?Sale $sale = null;

    /**
     * @var Collection<int, SaleProduct>
     */
    #[ORM\OneToMany(targetEntity: SaleProduct::class, mappedBy: 'product')]
    private Collection $saleProducts;

    public function __construct()
    {
        $this->saleProducts = new ArrayCollection();
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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

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

    public function getSale(): ?Sale
    {
        return $this->sale;
    }

    public function setSale(?Sale $sale): static
    {
        $this->sale = $sale;

        return $this;
    }

    /**
     * @return Collection<int, SaleProduct>
     */
    public function getSaleProducts(): Collection
    {
        return $this->saleProducts;
    }

    public function addSaleProduct(SaleProduct $saleProduct): static
    {
        if (!$this->saleProducts->contains($saleProduct)) {
            $this->saleProducts->add($saleProduct);
            $saleProduct->setProduct($this);
        }

        return $this;
    }

    public function removeSaleProduct(SaleProduct $saleProduct): static
    {
        if ($this->saleProducts->removeElement($saleProduct)) {
            // set the owning side to null (unless already changed)
            if ($saleProduct->getProduct() === $this) {
                $saleProduct->setProduct(null);
            }
        }

        return $this;
    }
}
