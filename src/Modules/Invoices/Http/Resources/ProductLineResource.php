<?php

declare(strict_types=1);

namespace Modules\Invoices\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Invoices\Domain\ProductLine;

/** @mixin ProductLine */
class ProductLineResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'quantity' => $this->quantity,
            'unit_price' => $this->unitPrice / 100,
            'total' => $this->total / 100,
        ];
    }
}
