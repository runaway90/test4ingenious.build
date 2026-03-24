<?php

declare(strict_types=1);

namespace Modules\Invoices\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Invoices\Domain\InvoiceRepositoryInterface;
use Modules\Invoices\Infrastructure\Persistence\InMemoryInvoiceRepository;

final class InvoiceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            InvoiceRepositoryInterface::class,
            InMemoryInvoiceRepository::class
        );
    }
}
