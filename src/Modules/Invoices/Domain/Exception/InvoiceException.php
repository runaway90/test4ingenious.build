<?php

declare(strict_types=1);

namespace Modules\Invoices\Domain\Exception;

use Exception;

final class InvoiceException extends Exception
{
    public static function alreadySent(): self
    {
        return new self('Cannot add product lines to an invoice that has already been sent.');
    }

    public static function notDraft(): self
    {
        return new self('Invoice can only be sent if it is in draft status.');
    }

    public static function emptyProductLines(): self
    {
        return new self('Invoice must contain at least one product line to be sent.');
    }

    public static function notSending(): self
    {
        return new self('Invoice can only be marked as sent-to-client if its status is sending.');
    }
}
