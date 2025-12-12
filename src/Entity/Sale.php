<?php

namespace App\Entity;

use App\Repository\SaleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SaleRepository::class)]
class Sale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255)]
    private ?string $paymentType = null;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'sale')]
    private Collection $salesProducts;

    /**
     * @var Collection<int, SaleProduct>
     */
    #[ORM\OneToMany(targetEntity: SaleProduct::class, mappedBy: 'sale')]
    private Collection $saleProducts;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $clientName = null;

    #[ORM\Column(nullable: true)]
    private ?bool $owingCompleted = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $client_mail = null;

    public function __construct()
    {
        $this->salesProducts = new ArrayCollection();
        $this->saleProducts = new ArrayCollection();
    }

    public function getTotal(): float
    {
        $total = 0;

        foreach ($this->saleProducts as $saleProduct) {
            $total += $saleProduct->getPrice() * $saleProduct->getQuantity();
        }

        return $total;
    }



    public function getId(): ?int
    {
        return $this->id;
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

    public function getPaymentType(): ?string
    {
        return $this->paymentType;
    }

    public function setPaymentType(string $paymentType): static
    {
        $this->paymentType = $paymentType;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getSalesProducts(): Collection
    {
        return $this->salesProducts;
    }

    public function addSalesProduct(Product $salesProduct): static
    {
        if (!$this->salesProducts->contains($salesProduct)) {
            $this->salesProducts->add($salesProduct);
            $salesProduct->setSale($this);
        }

        return $this;
    }

    public function removeSalesProduct(Product $salesProduct): static
    {
        if ($this->salesProducts->removeElement($salesProduct)) {
            // set the owning side to null (unless already changed)
            if ($salesProduct->getSale() === $this) {
                $salesProduct->setSale(null);
            }
        }

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
            $saleProduct->setSale($this);
        }

        return $this;
    }

    public function removeSaleProduct(SaleProduct $saleProduct): static
    {
        if ($this->saleProducts->removeElement($saleProduct)) {
            // set the owning side to null (unless already changed)
            if ($saleProduct->getSale() === $this) {
                $saleProduct->setSale(null);
            }
        }

        return $this;
    }

    public function getClientName(): ?string
    {
        return $this->clientName;
    }

    public function setClientName(?string $clientName): static
    {
        $this->clientName = $clientName;

        return $this;
    }

    public function isowingCompleted(): ?bool
    {
        return $this->owingCompleted;
    }

    public function setowingCompleted(?bool $owingCompleted): static
    {
        $this->owingCompleted = $owingCompleted;

        return $this;
    }

    public function getclientMail(): ?string
    {
        return $this->client_mail;
    }

    public function setclientMail(?string $client_mail): static
    {
        $this->client_mail = $client_mail;

        return $this;
    }
}
