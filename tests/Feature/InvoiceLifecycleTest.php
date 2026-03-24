<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class InvoiceLifecycleTest extends TestCase
{
    public function test_an_invoice_can_be_created_and_then_retrieved(): void
    {
        // 1. Create the invoice
        $response = $this->postJson('/api/invoices', [
            'customer_name' => 'John Doe',
            'customer_email' => 'john.doe@example.com',
        ]);

        $response
            ->assertStatus(201)
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('data.customer_name', 'John Doe')
                     ->where('data.status', 'draft')
                     ->has('data.id') // Ensure the ID exists
                     ->etc()
            );

        $invoiceId = $response->json('data.id');

        $getResponse = $this->getJson('/api/invoices/' . $invoiceId);

        $getResponse
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('data.id', $invoiceId)
                     ->where('data.customer_name', 'John Doe')
                     ->etc()
            );
    }
}
