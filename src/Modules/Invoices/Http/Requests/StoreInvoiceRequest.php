<?php

declare(strict_types=1);

namespace Modules\Invoices\Http\Requests;

// In a real Laravel application, this would extend FormRequest
class StoreInvoiceRequest
{
    public function __construct(public readonly array $data)
    {
        // In a real app, validation rules would be here.
        // For example:
        // 'customer_name' => 'required|string|max:255',
        // 'customer_email' => 'required|email',
    }

    public function validated(): array
    {
        return $this->data;
    }
}
