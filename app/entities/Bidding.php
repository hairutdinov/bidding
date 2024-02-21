<?php

namespace entities;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: 'bidding')]
#[ORM\HasLifecycleCallbacks]
class Bidding
{
    #[ORM\Id, ORM\Column(type: 'integer'), ORM\GeneratedValue]
    private int|null $id = null;

    #[ORM\Column(type: 'string')]
    private string|null $trade_number = null;

    #[ORM\Column(type: 'string')]
    private string|null $url = null;

    #[ORM\Column(type: 'string')]
    private string|null $benefit_information = null;

    #[ORM\Column(type: 'string')]
    private string|null $contact_person_email = null;

    #[ORM\Column(type: 'string')]
    private string|null $contact_person_phone_number = null;

    #[ORM\Column(type: Types::STRING)]
    private string|null $debtor_inn = null;

    #[ORM\Column(type: 'string')]
    private string|null $bankruptcy_case_number = null;

    #[ORM\Column(type: 'datetime')]
    private string|DateTime|null $date_of_bidding = null;

    #[ORM\Column(type: 'datetime')]
    private string|DateTime|null $created_at = null;

    #[ORM\Column(type: 'datetime')]
    private string|DateTime|null $updated_at = null;

    /** @var Collection<string, Lots> */
    #[ORM\OneToMany(targetEntity: Lots::class, mappedBy: 'bidding', indexBy: 'id')]
    private $lots;

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updated_at = new DateTime();
    }

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $this->created_at = new DateTime();
        $this->updated_at = new DateTime();
    }

    public function getTradeNumber()
    {
        return $this->trade_number;
    }

    public function setTradeNumber($trade_number): self
    {
        $this->trade_number = $trade_number;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function getBenefitInformation(): ?string
    {
        return $this->benefit_information;
    }

    public function setBenefitInformation(?string $benefit_information): self
    {
        $this->benefit_information = $benefit_information;
        return $this;
    }


    public function getContactPersonEmail(): ?string
    {
        return $this->contact_person_email;
    }

    public function setContactPersonEmail(?string $contact_person_email): self
    {
        $this->contact_person_email = $contact_person_email;
        return $this;
    }

    public function getContactPersonPhoneNumber(): ?string
    {
        return $this->contact_person_phone_number;
    }

    public function setContactPersonPhoneNumber(?string $contact_person_phone_number): self
    {
        $this->contact_person_phone_number = $contact_person_phone_number;
        return $this;
    }

    public function getDebtorInn(): ?string
    {
        return $this->debtor_inn;
    }

    public function setDebtorInn(?string $debtor_inn): self
    {
        $this->debtor_inn = $debtor_inn;
        return $this;
    }

    public function getBankruptcyCaseNumber(): ?string
    {
        return $this->bankruptcy_case_number;
    }

    public function setBankruptcyCaseNumber(?string $bankruptcy_case_number): self
    {
        $this->bankruptcy_case_number = $bankruptcy_case_number;
        return $this;
    }

    public function getLot(int $id): Lots
    {
        if (!isset($this->lots[$id])) {
            throw new \InvalidArgumentException("Лот с таким ID не найден");
        }

        return $this->lots[$id];
    }

    /** @return Collection<int, Lots> */
    public function getLots(): Collection
    {
        return $this->lots;
    }

    public function addLot(Lots $lot): self
    {
        $this->lots[] = $lot;
        $lot->setBidding($this);
        return $this;
    }

    public function getDateOfBidding(): ?string
    {
        return $this->date_of_bidding->format("m.d.Y H:i");
    }

    public function setDateOfBidding(string|DateTime $date_of_bidding): self
    {
        $this->date_of_bidding = $date_of_bidding;
        return $this;
    }

    public function getCreatedAt(): DateTime|string|null
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): DateTime|string|null
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(DateTime|string|null $updated_at): self
    {
        $this->updated_at = $updated_at;
        return $this;
    }
}