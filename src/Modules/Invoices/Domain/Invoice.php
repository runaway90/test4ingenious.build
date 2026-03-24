<?php

declare(strict_types=1);

namespace Modules\Invoices\Domain;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\Exception\InvoiceException;
use Webmozart\Assert\Assert;

class Invoice
{
    private string $id;
    private StatusEnum $status;
    private string $customerName;
    private string $customerEmail;
    private Collection $productLines;
    private int $totalPrice;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    private function __construct(
        string $id,
        string $customerName,
        string $customerEmail,
    ) {
        Assert::uuid($id);
        Assert::notEmpty($customerName);
        Assert::email($customerEmail);

        $this->id = $id;
        $this->status = StatusEnum::Draft;
        $this->customerName = $customerName;
        $this->customerEmail = $customerEmail;
        $this->productLines = new ArrayCollection();
        $this->totalPrice = 0;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public static function create(
        string $id,
        string $customerName,
        string $customerEmail,
    ): self {
        return new self($id, $customerName, $customerEmail);
    }

    public function addProductLine(ProductLine $productLine): void
    {
        if ($this->status !== StatusEnum::Draft) {
            throw InvoiceException::alreadySent();
        }

        $this->productLines->add($productLine);
        $this->recalculateTotal();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function send(): void
    {
        if ($this->status !== StatusEnum::Draft) {
            throw InvoiceException::notDraft();
        }

        if ($this->productLines->isEmpty()) {
            throw InvoiceException::emptyProductLines();
        }

        $this->status = StatusEnum::Sending;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function markAsSentToClient(): void
    {
        if ($this->status !== StatusEnum::Sending) {
            throw InvoiceException::notSending();
        }

        $this->status = StatusEnum::SentToClient;
        $this->updatedAt = new DateTimeImmutable();
    }

    private function recalculateTotal(): void
    {
        $this->totalPrice = $this->productLines->reduce(
            static fn (int $total, ProductLine $line): int => $total + $line->total,
            0
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getStatus(): StatusEnum
    {
        return $this->status;
    }

    public function getCustomerName(): string
    {
        return $this->customerName;
    }

    public function getCustomerEmail(): string
    {
        return $this->customerEmail;
    }

    /**
     * @return Collection<int, ProductLine>
     */
    public function getProductLines(): Collection
    {
        return $this->productLines;
    }

    public function getTotalPrice(): int
    {
        return $this->totalPrice;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
