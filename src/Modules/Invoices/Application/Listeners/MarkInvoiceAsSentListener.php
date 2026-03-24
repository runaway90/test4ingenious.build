<?php

declare(strict_types=1);

namespace Modules\Invoices\Application\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\Invoices\Domain\InvoiceRepositoryInterface;
use Modules\Notifications\Api\Events\WebhookDeliveredEvent;

final readonly class MarkInvoiceAsSentListener
{
    public function __construct(private InvoiceRepositoryInterface $repository)
    {
    }

    public function __invoke(WebhookDeliveredEvent $event): void
    {
        $invoiceId = $event->resourceId->toString();
        $invoice = $this->repository->findById($invoiceId);

        if (!$invoice) {
            Log::critical('Failed to mark invoice as sent: Invoice not found.', [
                'invoice_id' => $invoiceId,
            ]);
            return;
        }

        try {
            $invoice->markAsSentToClient();
            $this->repository->save($invoice);
        } catch (\Exception $e) {
            Log::error('Failed to process "markAsSentToClient" for invoice.', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
