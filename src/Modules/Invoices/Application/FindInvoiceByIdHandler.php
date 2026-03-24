<?php

declare(strict_types=1);

namespace Modules\Invoices\Application;

use Modules\Invoices\Domain\Exception\InvoiceNotFoundException;
use Modules\Invoices\Domain\Invoice;
use Modules\Invoices\Domain\InvoiceRepositoryInterface;

final readonly class FindInvoiceByIdHandler
{
    public function __construct(private InvoiceRepositoryInterface $repository)
    {
    }

    public function __invoke(string $id): Invoice
    {
        $invoice = $this->repository->findById($id);

        if (!$invoice) {
            throw new InvoiceNotFoundException($id);
        }

        return $invoice;
    }
}
