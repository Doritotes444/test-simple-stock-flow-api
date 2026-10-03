# Arquitectura Onion (Cebolla) - Backend API (Laravel)

## 1. Visión General
Este proyecto implementa una **Arquitectura Onion de 4 Capas** en PHP/Laravel para el sistema **Simple Stock Flow**. El objetivo fundamental es blindar las reglas del negocio frente a dependencias externas, bases de datos o frameworks web.

```
                  ┌─────────────────────────────────────────┐
                  │       4. PRESENTATION (Anillo 4)        │
                  │  Http: Controllers, Requests, Resources │
                  │  ProblemDetails (RFC 7807), Middlewares │
                  │                                         │
                  │   ┌─────────────────────────────────┐   │
                  │   │   3. INFRASTRUCTURE (Anillo 3)  │   │
                  │   │  Eloquent, Repositories, MySQL  │   │
                  │   │  JWT, Bcrypt, Storage, Logging  │   │
                  │   │                                 │   │
                  │   │   ┌─────────────────────────┐   │   │
                  │   │   │ 2. APPLICATION (Anillo 2│   │   │
                  │   │   │ UseCases, DTOs, Mappers │   │   │
                  │   │   │ Inbound/Outbound Ports  │   │   │
                  │   │   │                         │   │   │
                  │   │   │   ┌─────────────────┐   │   │   │
                  │   │   │   │1. DOMAIN (Anillo1│  │   │   │
                  │   │   │   │Entities, VO,    │   │   │   │
                  │   │   │   │Domain Exceptions│   │   │   │
                  │   │   │   │StockDomainServ  │   │   │   │
                  │   │   │   └─────────────────┘   │   │   │
                  │   │   └─────────────────────────┘   │   │
                  │   └─────────────────────────────────┘   │
                  └─────────────────────────────────────────┘
                                       ▲
                                       │
                  ┌─────────────────────────────────────────┐
                  │           COMPOSITION ROOT              │
                  │    PortBindingsServiceProvider.php      │
                  └─────────────────────────────────────────┘
```

## 2. Reglas de Dependencia
1. **El Dominio es Agnóstico**: El Anillo 1 (`Domain/`) es PHP puro y tiene CERO imports de `Illuminate\*` o librerías externas.
2. **Inversión de Dependencias (DIP)**: Los Casos de Uso en `Application` definen contratos de salida (`Ports/Outbound/`). La capa `Infrastructure` implementa estos puertos.
3. **Aislamiento de Presentación**: `Presentation` tiene **estrictamente prohibido** importar clases de `Infrastructure`. Solo interactúa con `Application/Ports/Inbound/` y DTOs.
4. **Composition Root**: El archivo `app/Composition/PortBindingsServiceProvider.php` es el **único lugar del sistema** autorizado para vincular los Puertos (`Application`) con los Adaptadores (`Infrastructure`) mediante el Service Container de Laravel.

## 3. Responsabilidades de las 4 Capas

### Anillo 1: `app/Domain/`
- **Model/**: Entidades de negocio ricas con validaciones invariantes (`Product`, `Sale`, `SaleItem`, `User`, `Category`).
- **ValueObject/**: Objetos de valor inmutables (`Money`, `Quantity`, `Stock`, `Email`, `Sku`).
- **Service/**: Lógica de dominio compleja multientidad (`StockDomainService`).
- **Exception/**: Errores de negocio (`InsufficientStockException`, `ImmutableSaleException`).

### Anillo 2: `app/Application/`
- **Ports/Inbound/**: Contratos de los casos de uso (`IPlaceSaleUseCasePort`, `ICreateProductUseCasePort`, etc.).
- **Ports/Outbound/**: Contratos de salida para repositorios y servicios (`IProductRepositoryPort`, `ISaleRepositoryPort`, `ITokenGeneratorPort`).
- **UseCase/**: Orquestadores de flujo de negocio (un archivo por caso de uso).
- **DTO/**: Objetos de transferencia desacoplados de HTTP y de Eloquent.

### Anillo 3: `app/Infrastructure/`
- **Persistence/Eloquent/**: Modelos de base de datos Eloquent y Repositorios concretos (`EloquentProductRepository`, `EloquentSaleRepository`).
- **Persistence/Mappers/**: Traductores bidireccionales entre Entidades de Dominio y Modelos Eloquent.
- **Security/**: Adaptadores de JWT y Hashing de contraseñas.
- **Logging/**: Adaptador de bitácora y monitoreo.

### Anillo 4: `app/Presentation/`
- **Http/Controller/**: Controladores REST que reciben la petición, delegan al Caso de Uso y devuelven la respuesta.
- **Http/Request/**: Form Requests de Laravel para validación sintáctica HTTP.
- **Http/Resource/**: Transformación de DTOs a formato JSON.
- **Http/ProblemDetails/**: Estandarización de errores bajo la norma RFC 7807.
- **Middleware/**: Filtros de autenticación JWT.
