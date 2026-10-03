# `test-simple-stock-flow-api`

Backend y API REST desarrollado en **Laravel / PHP 8.2+** bajo una **Arquitectura Onion de 4 Capas** estricta para el sistema de control de stock y ventas formativo del SENA.

## Estructura de Capas
- `app/Domain/` -> Núcleo Puro (Entidades, Value Objects, Servicios de Dominio, Excepciones).
- `app/Application/` -> Casos de Uso, DTOs, Puertos Inbound/Outbound.
- `app/Infrastructure/` -> Implementaciones técnicas (Eloquent Models, Repositorios, JWT, Hasher).
- `app/Presentation/` -> Controladores HTTP, Form Requests, Resources y ProblemDetails (RFC 7807).
- `app/Composition/` -> `PortBindingsServiceProvider.php` (Composition Root).

## Verificación de Arquitectura
Para validar que no existan violaciones de capas ni dependencias invertidas erróneas:
```bash
bash verify.sh
```
