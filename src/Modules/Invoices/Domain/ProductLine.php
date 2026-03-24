<?php

declare(strict_types=1);

namespace Modules\Invoices\Domain;

use Webmozart\Assert\Assert;

final readonly class ProductLine
{
    public string $id;
    public string $invoiceId;
    public string $name;
    public int $quantity;
    public int $unitPrice;
    public int $total;

    public function __construct(
        string $id,
        string $invoiceId,
        string $name,
        int $quantity,
        int $unitPrice,
    ) {
        Assert::uuid($id);
        Assert::uuid($invoiceId);
        Assert::notEmpty($name);
        Assert::greaterThan($quantity, 0);
        Assert::greaterThan($unitPrice, 0);

        $this->id = $id;
        $this->invoiceId = $invoiceId;
        $this->name = $name;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
        $this->total = $quantity * $unitPrice;
    }
}
