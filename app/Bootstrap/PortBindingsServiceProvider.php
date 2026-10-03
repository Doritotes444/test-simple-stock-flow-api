<?php

declare(strict_types=1);

namespace App\Bootstrap;

use Illuminate\Support\ServiceProvider;

// Inbound Ports (5 Puertos de Entrada según tareas de Tasks.md)
use App\Application\Ports\Inbound\IPlaceSaleUseCasePort;
use App\Application\Ports\Inbound\ICreateProductUseCasePort;
use App\Application\Ports\Inbound\IGetSalesReportUseCasePort;
use App\Application\Ports\Inbound\IAuthenticateUserUseCasePort;

// Use Cases Implementations (T-04, T-06, T-07, T-08, T-10)
use App\Application\UseCase\PlaceSaleUseCase;
use App\Application\UseCase\CreateProductUseCase;
use App\Application\UseCase\GetSalesReportUseCase;
use App\Application\UseCase\AuthenticateUserUseCase;

// Outbound Ports (10 Puertos de Salida según Artículo II de la Constitución)
use App\Application\Ports\Outbound\IProductRepositoryPort;
use App\Application\Ports\Outbound\ISaleRepositoryPort;
use App\Application\Ports\Outbound\IUserRepositoryPort;
use App\Application\Ports\Outbound\ITokenGeneratorPort;
use App\Application\Ports\Outbound\IPasswordHasherPort;
use App\Application\Ports\Outbound\IUnitOfWorkPort;

// Infrastructure Adapters (Persistencia, Seguridad, Storage)
use App\Infrastructure\Persistence\Repositories\EloquentProductRepository;
use App\Infrastructure\Persistence\Repositories\EloquentSaleRepository;
use App\Infrastructure\Persistence\Repositories\EloquentUserRepository;
use App\Infrastructure\Persistence\LaravelUnitOfWork;
use App\Infrastructure\Security\JwtTokenAdapter;
use App\Infrastructure\Security\BcryptHasherAdapter;

// Domain Services
use App\Domain\Service\StockDomainService;

class PortBindingsServiceProvider extends ServiceProvider
{
    /**
     * BOOTSTRAP (Composition Root):
     * Único archivo del backend autorizado para conocer simultáneamente
     * Puertos (Application) y Adaptadores (Infrastructure).
     */
    public function register(): void
    {
        // 1. Enlace de Puertos Outbound con Adaptadores Técnicos
        $this->app->singleton(IProductRepositoryPort::class, EloquentProductRepository::class);
        $this->app->singleton(ISaleRepositoryPort::class, EloquentSaleRepository::class);
        $this->app->singleton(IUserRepositoryPort::class, EloquentUserRepository::class);
        $this->app->singleton(IUnitOfWorkPort::class, LaravelUnitOfWork::class);
        $this->app->singleton(ITokenGeneratorPort::class, JwtTokenAdapter::class);
        $this->app->singleton(IPasswordHasherPort::class, BcryptHasherAdapter::class);

        // 2. Registro de Servicios de Dominio
        $this->app->singleton(StockDomainService::class);

        // 3. Enlace de Puertos Inbound con Casos de Uso
        $this->app->bind(IPlaceSaleUseCasePort::class, PlaceSaleUseCase::class);
        $this->app->bind(ICreateProductUseCasePort::class, CreateProductUseCase::class);
        $this->app->bind(IGetSalesReportUseCasePort::class, GetSalesReportUseCase::class);
        $this->app->bind(IAuthenticateUserUseCasePort::class, AuthenticateUserUseCase::class);
    }
}
