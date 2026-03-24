<?php

declare(strict_types=1);

namespace Modules\Invoices\Application;

use Modules\Invoices\Domain\Invoice;
use Modules\Invoices\Domain\InvoiceRepositoryInterface;
use Ramsey\Uuid\Uuid;

final readonly class CreateInvoiceHandler
{
    public function __construct(private InvoiceRepositoryInterface $repository)
    {
    }

    public function __invoke(string $customerName, string $customerEmail): Invoice
    {
        $invoice = Invoice::create(
            id: Uuid::uuid4()->toString(),
            customerName: $customerName,
            customerEmail: $customerEmail,
        );

        $this->repository->save($invoice);

        return $invoice;
    }
}
