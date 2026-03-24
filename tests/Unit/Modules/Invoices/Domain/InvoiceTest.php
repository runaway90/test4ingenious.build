<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Invoices\Domain;

use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\Exception\InvoiceException;
use Modules\Invoices\Domain\Invoice;
use Modules\Invoices\Domain\ProductLine;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    public function test_is_created_with_a_draft_status(): void
    {
        $invoice = Invoice::create(
            id: Uuid::uuid4()->toString(),
            customerName: 'Test Customer',
            customerEmail: 'test@example.com'
        );

        $this->assertSame(StatusEnum::Draft, $invoice->getStatus());
    }

    public function test_calculates_the_total_price_correctly(): void
    {
        $invoice = Invoice::create(
            id: Uuid::uuid4()->toString(),
            customerName: 'Test Customer',
            customerEmail: 'test@example.com'
        );

        $invoiceId = $invoice->getId();

        $invoice->addProductLine(new ProductLine(Uuid::uuid4()->toString(), $invoiceId, 'Product A', 1, 1000)); // 10.00
        $invoice->addProductLine(new ProductLine(Uuid::uuid4()->toString(), $invoiceId, 'Product B', 2, 2500)); // 50.00

        // Total should be 1000 + (2 * 2500) = 6000
        $this->assertSame(6000, $invoice->getTotalPrice());
    }

    public function test_can_be_sent_when_it_is_in_draft_status_and_has_product_lines(): void
    {
        $invoice = Invoice::create(
            id: Uuid::uuid4()->toString(),
            customerName: 'Test Customer',
            customerEmail: 'test@example.com'
        );
        $invoice->addProductLine(new ProductLine(Uuid::uuid4()->toString(), $invoice->getId(), 'Product A', 1, 1000));

        $invoice->send();

        $this->assertSame(StatusEnum::Sending, $invoice->getStatus());
    }

    public function test_throws_an_exception_when_trying_to_send_an_invoice_that_is_not_in_draft_status(): void
    {
        $this->expectException(InvoiceException::class);
        $this->expectExceptionMessage('Invoice can only be sent if it is in draft status.');

        $invoice = Invoice::create(
            id: Uuid::uuid4()->toString(),
            customerName: 'Test Customer',
            customerEmail: 'test@example.com'
        );
        $invoice->addProductLine(new ProductLine(Uuid::uuid4()->toString(), $invoice->getId(), 'Product A', 1, 1000));

        $invoice->send(); // First send is successful
        $invoice->send(); // Second send should fail
    }

    public function test_throws_an_exception_when_trying_to_send_an_invoice_with_no_product_lines(): void
    {
        $this->expectException(InvoiceException::class);
        $this->expectExceptionMessage('Invoice must contain at least one product line to be sent.');

        $invoice = Invoice::create(
            id: Uuid::uuid4()->toString(),
            customerName: 'Test Customer',
            customerEmail: 'test@example.com'
        );

        $invoice->send();
    }

    public function test_can_be_marked_as_sent_to_client_only_when_its_status_is_sending(): void
    {
        $invoice = Invoice::create(
            id: Uuid::uuid4()->toString(),
            customerName: 'Test Customer',
            customerEmail: 'test@example.com'
        );
        $invoice->addProductLine(new ProductLine(Uuid::uuid4()->toString(), $invoice->getId(), 'Product A', 1, 1000));
        $invoice->send();

        $invoice->markAsSentToClient();

        $this->assertSame(StatusEnum::SentToClient, $invoice->getStatus());
    }

    public function test_throws_an_exception_when_trying_to_mark_as_sent_from_a_status_other_than_sending(): void
    {
        $this->expectException(InvoiceException::class);
        $this->expectExceptionMessage('Invoice can only be marked as sent-to-client if its status is sending.');

        $invoice = Invoice::create(
            id: Uuid::uuid4()->toString(),
            customerName: 'Test Customer',
            customerEmail: 'test@example.com'
        );

        $invoice->markAsSentToClient();
    }

    public function test_throws_an_exception_when_adding_a_product_line_to_an_already_sent_invoice(): void
    {
        $this->expectException(InvoiceException::class);
        $this->expectExceptionMessage('Cannot add product lines to an invoice that has already been sent.');

        $invoice = Invoice::create(
            id: Uuid::uuid4()->toString(),
            customerName: 'Test Customer',
            customerEmail: 'test@example.com'
        );
        $invoice->addProductLine(new ProductLine(Uuid::uuid4()->toString(), $invoice->getId(), 'Product A', 1, 1000));
        $invoice->send();

        // Now the invoice is in "sending" status, which should prevent adding more lines
        $invoice->addProductLine(new ProductLine(Uuid::uuid4()->toString(), $invoice->getId(), 'Product B', 1, 500));
    }
}
