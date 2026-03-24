<?php

declare(strict_types=1);

namespace Modules\Invoices\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Invoices\Application\CreateInvoiceHandler;
use Modules\Invoices\Application\FindInvoiceByIdHandler;
use Modules\Invoices\Application\SendInvoiceHandler;
use Modules\Invoices\Http\Requests\StoreInvoiceRequest;
use Modules\Invoices\Http\Resources\InvoiceResource;
use Symfony\Component\HttpFoundation\Response;

final class InvoiceController extends Controller
{
    public function store(StoreInvoiceRequest $request, CreateInvoiceHandler $handler): JsonResponse
    {
        $validated = $request->validated();

        $invoice = ($handler)(
            customerName: $validated['customer_name'],
            customerEmail: $validated['customer_email']
        );

        return (new InvoiceResource($invoice))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(string $id, FindInvoiceByIdHandler $handler): InvoiceResource
    {
        $invoice = ($handler)($id);

        return new InvoiceResource($invoice);
    }

    public function send(string $id, SendInvoiceHandler $handler): JsonResponse
    {
        ($handler)($id);

        return new JsonResponse(null, Response::HTTP_ACCEPTED);
    }
}
