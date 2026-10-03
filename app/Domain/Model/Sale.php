<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Exception\DomainValidationException;
use App\Domain\ValueObject\Money;
use DateTimeImmutable;

class Sale
{
    private ?int $id;
    private int $userId;
    private Money $total;
    /** @var array<SaleItem> */
    private array $items;
    private DateTimeImmutable $createdAt;

    /**
     * @param array<SaleItem> $items
     */
    public function __construct(
        ?int $id,
        int $userId,
        array $items,
        ?Money $total = null,
        ?DateTimeImmutable $createdAt = null
    ) {
        if (empty($items)) {
            throw new DomainValidationException("Una venta debe contener al menos un producto");
        }

        $this->id = $id;
        $this->userId = $userId;
        $this->items = $items;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();

        if ($total === null) {
            $computedTotal = new Money(0, $items[0]->getUnitPrice()->getCurrency());
            foreach ($items as $item) {
                $computedTotal = $computedTotal->add($item->getSubtotal());
            }
            $this->total = $computedTotal;
        } else {
            $this->total = $total;
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getTotal(): Money
    {
        return $this->total;
    }

    /**
     * @return array<SaleItem>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
