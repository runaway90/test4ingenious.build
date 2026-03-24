<?php

declare(strict_types=1);

namespace Modules\Invoices\Http\Resources;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Invoices\Domain\Invoice;

/** @mixin Invoice */
class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getId(),
            'status' => $this->getStatus()->value,
            'customer_name' => $this->getCustomerName(),
            'customer_email' => $this->getCustomerEmail(),
            'total_price' => $this->getTotalPrice() / 100,
            'product_lines' => ProductLineResource::collection($this->getProductLines()->toArray()),
            'created_at' => $this->getCreatedAt()->format(DateTime::ATOM),
            'updated_at' => $this->getUpdatedAt()->format(DateTime::ATOM),
        ];
    }
}
