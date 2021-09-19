<?php

declare(strict_types=1);

namespace App\Model;

class PaginatedDataModel
{
    /**
     * @var int
     */
    public int $total;

    /**
     * @var int
     */
    public int $pages;

    /**
     * @var int
     */
    public int $limit;

    /**
     * @var int
     */
    public int $page;

    /**
     * @var int|null
     */
    public ?int $prev;

    /**
     * @var int|null
     */
    public ?int $next;

    /**
     * @var array
     */
    public array $items;

    /**
     * @param int $total
     * @param int $limit
     * @param int $page
     * @param array $items
     */
    public function __construct(int $total, int $limit, int $page, array $items)
    {
        $this->total = $total;
        $this->limit = $limit;
        $this->page = $page;
        $this->items = $items;
        $this->pages = 0;
        $this->prev = null;
        $this->next = null;

        if ($this->total > 0) {
            $this->pages = intval(ceil($this->total / $this->limit));

            if ($this->pages > 1) {
                if ($this->page !== $this->pages) {
                    $this->next = $this->page + 1;
                }

                if ($this->page !== 1) {
                    $this->prev = $this->page - 1;
                }
            } else {
                $this->page = 1;
            }
        }
    }
}
