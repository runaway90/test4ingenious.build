<?php

declare(strict_types=1);

namespace Modules\Invoices\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Modules\Invoices\Application\Listeners\MarkInvoiceAsSentListener;
use Modules\Invoices\Domain\InvoiceRepositoryInterface;
use Modules\Invoices\Infrastructure\Persistence\InMemoryInvoiceRepository;
use Modules\Notifications\Api\Events\WebhookDeliveredEvent;

final class InvoiceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            InvoiceRepositoryInterface::class,
            InMemoryInvoiceRepository::class
        );
    }

    public function boot(): void
    {
        Event::listen(
            WebhookDeliveredEvent::class,
            MarkInvoiceAsSentListener::class
        );
    }
}
