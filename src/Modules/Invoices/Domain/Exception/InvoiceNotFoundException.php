<?php

declare(strict_types=1);

namespace Modules\Invoices\Domain\Exception;

use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final class InvoiceNotFoundException extends RuntimeException
{
    public function __construct(string $id)
    {
        parent::__construct(
            message: sprintf('Invoice with ID "%s" not found.', $id),
            code: Response::HTTP_NOT_FOUND
        );
    }
}
