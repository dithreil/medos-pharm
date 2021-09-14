<?php

declare(strict_types=1);

namespace App\Entity\Document;

use App\Entity\Change\PriceChange;
use App\Entity\Store;
use App\Exception\AppException;
use App\Repository\Document\PriceDocumentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PriceDocumentRepository::class)
 * @ORM\Table(name="price_documents")
 */
class PriceDocument extends BaseDocument
{
    /**
     * @var Store
     * @ORM\ManyToOne(targetEntity=Store::class, inversedBy="priceDocuments")
     */
    private Store $store;

    /**
     * @var Income|null
     * @ORM\OneToOne(targetEntity="Income", mappedBy="priceDocument")
     * @ORM\JoinColumn(name="income_id", referencedColumnName="id", nullable=true)
     */
    private ?Income $income;

    /**
     * @var Collection|PriceChange[]
     * @ORM\OneToMany(targetEntity=PriceChange::class, mappedBy="document")
     */
    protected Collection $priceChanges;

    /**
     * @param Store $store
     * @param string $type
     * @param bool $isSet
     * @throws AppException
     */
    public function __construct(Store $store, string $type, bool $isSet = false)
    {
        parent::__construct($type, $isSet);
        $this->store = $store;

        $this->priceChanges = new ArrayCollection();
    }

    /**
     * @return Store
     */
    public function getStore(): Store
    {
        return $this->store;
    }

    /**
     * @param Store $store
     */
    public function setStore(Store $store): void
    {
        $this->store = $store;
    }

    /**
     * @return Income|null
     */
    public function getIncome(): ?Income
    {
        return $this->income;
    }

    /**
     * @param Income|null $income
     */
    public function setIncome(?Income $income): void
    {
        $this->income = $income;
    }

    /**
     * @return Collection|PriceChange[]
     */
    public function getPriceChanges(): Collection
    {
        return $this->priceChanges;
    }

    /**
     * @param PriceChange $priceChange
     */
    public function addPriceChange(PriceChange $priceChange): void
    {
        if ($this->priceChanges->contains($priceChange)) {
            return;
        }

        $this->priceChanges->add($priceChange);
        $priceChange->setDocument($this);
    }

    /**
     * @param PriceChange $priceChange
     */
    public function removePriceChange(PriceChange $priceChange): void
    {
        if (!$this->priceChanges->contains($priceChange)) {
            return;
        }

        $this->priceChanges->removeElement($priceChange);
    }
}
