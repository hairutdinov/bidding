<?php


namespace entities;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity]
#[ORM\Table(name: 'lots')]
class Lots
{
    #[ORM\Id, ORM\Column(type: 'integer'), ORM\GeneratedValue]
    private int|null $id = null;

    #[ORM\ManyToOne(targetEntity: Bidding::class, inversedBy: 'lots')]
    #[ORM\JoinColumn(name: 'bidding_id', referencedColumnName: 'id')]
    private Bidding $bidding;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'string')]
    private string $lot_number;

    #[ORM\Column(type: 'string')]
    private string|null $starting_price = null;

    public function getLotNumber(): ?string
    {
        return $this->lot_number;
    }

    public function setLotNumber(?string $lot_number): self
    {
        $this->lot_number = $lot_number;
        return $this;
    }

    public function getStartingPrice(): ?string
    {
        return $this->starting_price;
    }

    public function setStartingPrice(?string $starting_price): self
    {
        $this->starting_price = $starting_price;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getBidding(): Bidding
    {
        return $this->bidding;
    }

    public function setBidding(Bidding $bidding): self
    {
        $this->bidding = $bidding;
        return $this;
    }

    public function getBiddingId(): ?int
    {
        return $this->bidding_id;
    }

    public function setBiddingId(?int $bidding_id): void
    {
        $this->bidding_id = $bidding_id;
    }
}