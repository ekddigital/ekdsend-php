<?php

declare(strict_types=1);

namespace EKDSend\Models;

/**
 * Paginated response model
 *
 * @template T
 */
class PaginatedResponse
{
    /** @var array<T> */
    public readonly array $data;
    public readonly int $total;
    public readonly int $limit;
    public readonly int $offset;

    /**
     * @param array<T> $data
     * @param int $total
     * @param int $limit
     * @param int $offset
     */
    public function __construct(array $data, int $total, int $limit, int $offset)
    {
        $this->data = $data;
        $this->total = $total;
        $this->limit = $limit;
        $this->offset = $offset;
    }

    /**
     * Check if there are more pages
     */
    public function hasMore(): bool
    {
        return ($this->offset + $this->limit) < $this->total;
    }

    /**
     * Get next page offset
     */
    public function nextOffset(): ?int
    {
        if (!$this->hasMore()) {
            return null;
        }
        return $this->offset + $this->limit;
    }
}
