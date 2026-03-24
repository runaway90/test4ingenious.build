<?php

declare(strict_types=1);

namespace Modules\Invoices\Infrastructure\Persistence;

use Modules\Invoices\Domain\Invoice;
use Modules\Invoices\Domain\InvoiceRepositoryInterface;

final class InMemoryInvoiceRepository implements InvoiceRepositoryInterface
{
    /** @var array<string, Invoice> */
    private static array $invoices = [];

    public function findById(string $id): ?Invoice
    {
        return self::$invoices[$id] ?? null;
    }

    public function save(Invoice $invoice): void
    {
        self::$invoices[$invoice->getId()] = $invoice;
    }
}
