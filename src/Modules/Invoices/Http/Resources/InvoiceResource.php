<?php

declare(strict_types=1);

namespace Modules\Invoices\Http\Resources;

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
            'product_lines' => ProductLineResource::collection($this->getProductLines()),
            'created_at' => $this->getCreatedAt()->toIso8601String(),
            'updated_at' => $this->getUpdatedAt()->toIso8601String(),
        ];
    }
}
