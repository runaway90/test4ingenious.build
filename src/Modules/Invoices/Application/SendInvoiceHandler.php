<?php

declare(strict_types=1);

namespace Modules\Invoices\Application;

use Modules\Invoices\Domain\Exception\InvoiceNotFoundException;
use Modules\Invoices\Domain\InvoiceRepositoryInterface;
use Modules\Notifications\Api\Dtos\NotifyData;
use Modules\Notifications\Api\NotificationFacadeInterface;
use Ramsey\Uuid\Uuid;

final readonly class SendInvoiceHandler
{
    public function __construct(
        private InvoiceRepositoryInterface $repository,
        private NotificationFacadeInterface $facade,
    ) {
    }

    public function __invoke(string $id): void
    {
        $invoice = $this->repository->findById($id);

        if (!$invoice) {
            throw new InvoiceNotFoundException($id);
        }
        $invoice->send();

        $this->facade->notify(new NotifyData(
            resourceId: Uuid::fromString($invoice->getId()),
            toEmail: $invoice->getCustomerEmail(),
            subject: 'Your invoice is on its way!',
            message: 'Hello ' . $invoice->getCustomerName() . ', your invoice is being processed.',
        ));

        $this->repository->save($invoice);
    }
}
