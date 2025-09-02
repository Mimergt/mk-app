# 📦 INSTRUCCIONES DE BACKUP V1

## 🎯 ¿Qué contiene este backup?

### ✅ Sistema Completo Funcional:
- **Fecha de backup**: $(date)
- **Commit Git**: d0fe8b3 - "V1 CHECKPOINT"
- **Tag Git**: v1.0
- **Estado**: Completamente funcional y probado

### 📁 Archivos del Backup:
1. `laravel-app-v1-source.tar.gz` - Código fuente completo (846KB)
2. `database-v1.sqlite` - Base de datos SQLite
3. `README-V1.md` - Documentación completa del sistema
4. `BACKUP-INSTRUCTIONS.md` - Estas instrucciones

## 🚀 RESTAURACIÓN COMPLETA - PASO A PASO

### 1️⃣ Extraer el código fuente:
```bash
cd /var/www/html
tar -xzf mk-app/backups/v1/laravel-app-v1-source.tar.gz -C mk-app-v1-restored/
```

### 2️⃣ Restaurar base de datos:
```bash
cd mk-app-v1-restored
cp backups/v1/database-v1.sqlite database/database.sqlite
```

### 3️⃣ Configurar entorno:
```bash
# Instalar dependencias
composer install
npm install

# Configurar aplicación
cp .env.example .env
php artisan key:generate

# Configurar base de datos en .env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

### 4️⃣ Ejecutar el sistema:
```bash
php artisan serve
```

### 5️⃣ Acceder al panel:
- URL: http://localhost:8000/admin
- Crear usuario: `php artisan make:filament-user`

## 🎯 FUNCIONALIDADES INCLUIDAS

### ✅ Páginas Principales:
- `/admin/gestion-bombas` - Gestión de bombas por gasolinera
- `/admin/gastos` - Gestión de gastos operativos mensuales  
- `/admin/precios` - Gestión de precios compra/venta

### ✅ Características Técnicas:
- Laravel 11 + Filament 3.3
- Base de datos SQLite integrada
- Sistema de navegación entre páginas (NO tabs)
- Gastos mensuales con adicionales dinámicos
- Precios con promedios calculados desde DB
- Estados operativos de bombas

## 🔄 ALTERNATIVA: Usar Git

### Si prefieres usar Git:
```bash
# Clonar desde el commit específico
git clone /path/to/repo mk-app-v1
cd mk-app-v1
git checkout v1.0

# Instalar y configurar
composer install
npm install
cp .env.example .env
php artisan key:generate
```

## ⚠️ NOTAS IMPORTANTES

### 💡 Sobre el Sistema:
- **Arquitectura estable**: Sin problemas de tabs
- **Base de datos**: Incluye datos de prueba (MONTEKARLO I & II)
- **Funcionalidad completa**: Todo operativo y probado

### 🚀 Para Desarrollo V2:
- Este backup preserva V1 intacto
- Puedes desarrollar V2 en paralelo
- Rollback disponible en cualquier momento

### 🛠️ Solución de Problemas:
- Si hay errores de permisos: `sudo chown -R www-data:www-data storage/ bootstrap/cache/`
- Si falta SQLite: `sudo apt-get install php-sqlite3`
- Si fallan migraciones: La DB ya está creada con datos

---
**V1 BACKUP CREADO EXITOSAMENTE** ✅  
**Estado**: Listo para restauración completa  
**Garantía**: Sistema 100% funcional preservado
