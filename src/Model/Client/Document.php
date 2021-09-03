<?php

declare(strict_types=1);

namespace App\Model\Client;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Document
{
    /**
     * @var int
     */
    public int $code;

    /**
     * @var string
     */
    public string $name;

    /**
     * @var Collection
     */
    public Collection $categories;

    /**
     * Document constructor.
     * @param int $code
     * @param string $name
     */
    public function __construct(int $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;
        $this->categories = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @param int $code
     * @return $this
     */
    public function setCode(int $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|string[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * @param string $category
     */
    public function addCategory(string $category): void
    {
        if ($this->categories->contains($category)) {
            return;
        }

        $this->categories->add($category);
    }

    /**
     * @param string $category
     */
    public function removeCategory(string $category): void
    {
        if (!$this->categories->contains($category)) {
            return;
        }

        $this->categories->removeElement($category);
    }
}
