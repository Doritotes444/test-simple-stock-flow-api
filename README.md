# Simple Stock Flow · Backend REST API (PHP / Laravel)

> **Prueba técnica SDD · Ficha ADSO 3413974**  
> Implementación del backend transaccional bajo Arquitectura Limpia (Onion / Hexagonal) con PHP 8.2 y Laravel 11.

---

## 1. ¿Qué es este repositorio y qué rol cumple en Simple Stock Flow?

Este repositorio contiene la **API REST transaccional** que gobierna las reglas de negocio, la persistencia en MySQL y el control de acceso en *Simple Stock Flow*.

Cumple el rol de **núcleo del sistema**, gestionando:
- **Autenticación y Seguridad (RBAC):** Emisión y validación de tokens JWT (HS256 con tolerancia de 30s) y control de acceso basado en roles (`admin` y `seller`). Cumple DP-04 prohibiendo la creación de nuevos administradores.
- **Catálogo de Productos:** Alta, edición, soft delete, paginación, filtros y subida de imágenes con validación de tipo MIME (JPEG/PNG/WebP hasta 5 MB) y almacenamiento en volumen `/var/www/media`.
- **Punto de Venta Transaccional:** Registro atómico de ventas con bloqueo optimista (`version` con 3 reintentos automáticos), deducción inmediata de stock e invariante de líneas con productos únicos.
- **Reportes Consolidados:** Consultas optimizadas con retención del nombre histórico congelado del producto vendido (DP-01) y cálculo dinámico de subtotales y totales.
- **Dueño del Esquema (ADR-001):** Gestiona las 2 migraciones maestras (esquema estricto con 25 columnas, 21 constraints, 13 índices, 9 checks y 5 categorías fijas).

---

## 2. ¿Cómo se ejecuta localmente?

### Con Docker Compose (Recomendado)
El servicio se levanta junto con la base de datos MySQL y la SPA desde `test-simple-stock-flow-infra`:

```bash
cd ../test-simple-stock-flow-infra
docker compose up -d --build api
```
La API estará disponible en `http://localhost:8000`.

### Sin Docker (Desarrollo Local con PHP 8.2+)
Requiere PHP 8.2+ con extensiones `pdo_mysql`, `mbstring`, `openssl`, y MySQL corriendo localmente:

```bash
# 1. Instalar dependencias con Composer
composer install

# 2. Configurar entorno
cp .env.example .env
# Configurar DB_DATABASE, DB_USERNAME, DB_PASSWORD, JWT_SIGNING_KEY en .env

# 3. Ejecutar migraciones de base de datos
php artisan migrate --force

# 4. Sembrar el usuario administrador inicial
php artisan app:bootstrap-admin

# 5. Iniciar el servidor local
php artisan serve --port=8000
```

---

## 3. Variables de entorno requeridas

El archivo `.env` controla el funcionamiento de la API:

| Variable | Descripción | Valor por Defecto / Ejemplo |
|---|---|---|
| `APP_ENV` | Entorno de ejecución | `production` o `local` |
| `APP_DEBUG` | Mostrar trazas de error | `false` |
| `APP_URL` | URL base del backend | `http://localhost:8000` |
| `DB_CONNECTION` | Motor de persistencia | `mysql` |
| `DB_HOST` | Host del motor MySQL | `db` (en Docker) o `127.0.0.1` |
| `DB_PORT` | Puerto de conexión MySQL | `3306` |
| `DB_DATABASE` | Base de datos | `stockflow` |
| `DB_USERNAME` | Usuario MySQL | `stockflow` |
| `DB_PASSWORD` | Contraseña MySQL | `stockflowpass` |
| `JWT_SIGNING_KEY` | Clave secreta simétrica HS256 | Requerido (mínimo 32 caracteres) |
| `ADMIN_EMAIL` | Correo del admin inicial | `admin@stockflow.com` |
| `ADMIN_PASSWORD` | Contraseña del admin inicial | Requerido (mínimo 8 caracteres) |
| `MEDIA_ROOT` | Directorio físico para imágenes | `/var/www/media` |

---

## 4. ¿Cómo se ejecutan las pruebas?

```bash
# Pruebas automatizadas con PHPUnit
php artisan test
```

---

## 5. Decisiones técnicas relevantes tomadas durante la implementación

1. **Arquitectura Onion Estricta (Artículo I de la Constitución):**
   - **`Domain/` (Puro PHP):** Cero dependencias de Laravel, Eloquent o librerías externas. Contiene entidades (`Product`, `Sale`, `SaleItem`, `User`, `Category`), Value Objects (`Money`, `Quantity`, `DateRange`), excepciones en español y contratos de repositorios puramente tipados.
   - **`Application/`:** Casos de uso atómicos (1 acción = 1 clase) y DTOs planos.
   - **`Infrastructure/`:** Modelos Eloquent (`ProductModel`, `SaleModel`, etc.) separados de las entidades de dominio, con Mappers bidireccionales explícitos que previenen la fuga de abstracciones.
   - **`Presentation/`:** Controladores HTTP, Form Requests que retornan `400 application/problem+json` y Middleware JWT.
2. **Cumplimiento Invariante D-C9 (Respuestas Vacías):**
   - Las respuestas de error 401 (No autenticado), 403 (No autorizado), 404 (No encontrado) y 405 (Método no permitido) se retornan con cuerpo estrictamente vacío (`Content-Length: 0`), mientras que los errores 400 y 422 utilizan el estándar RFC 7807 (`application/problem+json; charset=utf-8`).
3. **Bloqueo Optimista y Manejo de Concurrencia (RN-11 / 409):**
   - El caso de uso `RegisterSaleUseCase` implementa reintentos automáticos (hasta 3 intentos) capturando condiciones de carrera en el stock de los productos. Si la colisión persiste, emite `ConcurrencyConflictException` mapeada a HTTP 409 con mensaje en español.
4. **Reporte con Nombres Congelados (DP-01):**
   - El adaptador `DatabaseSalesReportQuery` utiliza consultas SQL agregadas con particionamiento y ventana temporal para extraer el nombre congelado más reciente de cada producto dentro del rango solicitado.
5. **Generador y Validador JWT Autónomo:**
   - La clase `JwtTokenGenerator` implementa codificación HS256 nativa en PHP sin dependencias de paquetes externos pesados, incluyendo una ventana de gracia (*leeway*) de 30 segundos para tolerancia ante desfases de reloj en entornos distribuidos.
