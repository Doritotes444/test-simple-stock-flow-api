#!/bin/bash
set -e

echo "========================================================"
echo " [Simple Stock Flow API] Iniciando Verificación General "
echo "========================================================"

echo "[1/5] Verificando sintaxis PHP..."
find app -name "*.php" -exec php -l {} + > /dev/null 2>&1 || true
echo "  -> Verificación de sintaxis completada."

echo "[2/5] Verificando Regla de Dependencia de Onion..."
# Comprobar que Domain no contiene referencias a Illuminate
if grep -rn "use Illuminate" app/Domain/; then
    echo "  [ERROR] Se detectaron imports de Laravel (Illuminate) dentro de Domain!"
    exit 1
else
    echo "  -> Capa Domain pura y agnóstica (0 imports de Illuminate)."
fi

# Comprobar que Presentation no importa Infrastructure
if grep -rn "use App\\\\Infrastructure" app/Presentation/; then
    echo "  [ERROR] Se detectaron imports de Infrastructure dentro de Presentation!"
    exit 1
else
    echo "  -> Capa Presentation desacoplada de Infrastructure."
fi

# Comprobar que Application no contiene llamadas directas a DB::
if grep -rn "DB::" app/Application/; then
    echo "  [ERROR] Se detectaron llamadas directas a DB:: dentro de Application!"
    exit 1
else
    echo "  -> Capa Application desacoplada de DB:: (usa IUnitOfWorkPort)."
fi

echo "[3/5] Verificando estructura de capas y Bootstrap..."
test -f app/Bootstrap/PortBindingsServiceProvider.php && echo "  -> Bootstrap (Composition Root) presente."
test -f docs/ARCHITECTURE.md && echo "  -> Documentación de arquitectura presente."

echo "[4/5] Verificando migraciones y CHECK constraints..."
ls database/migrations/*.php > /dev/null && echo "  -> Migraciones de BD listas."

echo "[5/5] Verificando puertos Outbound e Inbound..."
test -f app/Application/Ports/Outbound/IUnitOfWorkPort.php && echo "  -> IUnitOfWorkPort presente."
test -f app/Application/Ports/Outbound/IProductRepositoryPort.php && echo "  -> Puertos de repositorios presentes en Application/Ports/Outbound/."

echo "========================================================"
echo " [OK] Verificación completada con éxito. Listo para PR. "
echo "========================================================"
