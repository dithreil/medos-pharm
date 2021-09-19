<?php

declare(strict_types=1);

namespace App\Entity\Document;

use App\Entity\Change\StockChange;
use App\Entity\Store;
use App\Exception\AppException;
use App\Repository\Document\StockDocumentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StockDocumentRepository::class)
 * @ORM\Table(name="stock_documents")
 */
class StockDocument extends BaseDocument
{
    /**
     * @var Store
     * @ORM\ManyToOne(targetEntity=Store::class, inversedBy="stockDocuments")
     */
    private Store $store;

    /**
     * @var Income|null
     * @ORM\OneToOne(targetEntity="Income", mappedBy="stockDocument")
     * @ORM\JoinColumn(name="income_id", referencedColumnName="id", nullable=true)
     */
    private ?Income $income;

    /**
     * @var Collection|StockChange[]
     * @ORM\OneToMany(targetEntity=StockChange::class, mappedBy="document")
     */
    protected Collection $stockChanges;

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

        $this->stockChanges = new ArrayCollection();
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
     * @return Collection|StockChange[]
     */
    public function getStockChanges(): Collection
    {
        return $this->stockChanges;
    }

    /**
     * @param StockChange $stockChange
     */
    public function addStockChange(StockChange $stockChange): void
    {
        if ($this->stockChanges->contains($stockChange)) {
            return;
        }

        $this->stockChanges->add($stockChange);
        $stockChange->setDocument($this);
    }

    /**
     * @param StockChange $stockChange
     */
    public function removeStockChange(StockChange $stockChange): void
    {
        if (!$this->stockChanges->contains($stockChange)) {
            return;
        }

        $this->stockChanges->removeElement($stockChange);
    }

    /**
     * @param bool $isSet
     */
    public function setRowsIsSet(bool $isSet): void
    {
        foreach ($this->getStockChanges() as $stockChange) {
            $stockChange->setIsSet($isSet);
        }
    }
}
