# Sistema de Gestión de Gasolineras

Sistema completo de gestión para cadena de gasolineras desarrollado en Laravel 11 con panel administrativo Filament PHP. Incluye PWA para operadores móviles y gestión completa de turnos, ventas, inventarios y lecturas de bombas.

## 🚀 Características Principales

- **Panel Administrativo**: Interface completa con Filament PHP para gestión centralizada
- **PWA Móvil**: Aplicación web progresiva para operadores en campo
- **Gestión de Turnos**: Control completo de turnos de trabajo por gasolinera
- **Control de Ventas**: Seguimiento de ventas por crédito, tarjetas, efectivo y descuentos
- **Inventario de Combustibles**: Monitoreo de tanques en pulgadas y galones
- **Lecturas de Bombas**: Registro fotográfico y numérico de todas las bombas
- **Multi-tenant**: Soporte para múltiples gasolineras en una sola instalación

## 🏗️ Arquitectura Técnica

### Stack Tecnológico
- **Backend**: Laravel 11 (PHP 8.2+)
- **Admin Panel**: Filament PHP v3
- **Frontend**: Blade + Tailwind CSS
- **PWA**: Service Workers + Manifest
- **Base de Datos**: MySQL 8.0
- **Compresión de Imágenes**: Automática con Laravel
- **Control de Versiones**: Git + GitHub

### Estructura de Datos Principal

#### Tablas Principales
- `gasolineras`: Información de cada estación
- `users`: Usuarios y operadores del sistema
- `turnos`: Registros de turnos de trabajo
- `bombas`: Información de bombas por gasolinera
- `turno_bomba_datos`: Lecturas específicas de bombas por turno

## 📊 Funcionalidades por Módulo

### Panel de Operador (/gas/)
**Ubicación**: `resources/views/turnos/panel.blade.php`

#### Gestión de Turnos
- Inicio y cierre de turnos
- Control de dinero en caja (apertura/cierre)
- Estados: abierto, cerrado, pausado

#### Totales de Ventas
- **Venta Crédito**: Ventas a cuentas corporativas
- **Venta Tarjetas**: Pagos con tarjeta de crédito/débito
- **Venta Efectivo**: Transacciones en efectivo
- **Venta Descuentos**: Promociones y descuentos aplicados

#### Nivel de Tanques
- **Combustibles**: Super, Regular, Diesel
- **Medidas**: Pulgadas y galones por tipo
- **Validación**: Rangos permitidos y valores numéricos

#### Lecturas de Bombas
- Registro fotográfico obligatorio
- Lecturas de galonaje por combustible
- Lectura de contador de dinero (CC)
- Observaciones y notas

### Panel Administrativo (/admin/)
**Ubicación**: `app/Filament/Resources/TurnoResource.php`

#### Vista de Turnos
```php
// Columnas principales disponibles
- ID del turno
- Gasolinera y operador
- Fecha y horarios
- Estado del turno
- Totales de ventas
- Niveles de tanques
- Dinero en caja
```

#### Filtros Avanzados
- Por gasolinera
- Por operador
- Por rango de fechas
- Por estado de turno
- Por rangos de ventas

#### Relaciones de Datos
- **TurnoBombaDatosRelationManager**: Gestión de lecturas de bombas
- Vista detallada con fotografías
- Edición inline de datos
- Validaciones en tiempo real

## 🗄️ Estructura de Base de Datos

### Tabla `turnos`
```sql
-- Campos principales
id, gasolinera_id, user_id, fecha
hora_inicio, hora_fin, estado
dinero_apertura, dinero_cierre

-- Totales de ventas (agregados en migración 2025_09_13)
venta_credito, venta_tarjetas
venta_efectivo, venta_descuentos

-- Niveles de tanques (agregados en migración 2025_09_13)
tanque_super_pulgadas, tanque_regular_pulgadas, tanque_diesel_pulgadas
tanque_super_galones, tanque_regular_galones, tanque_diesel_galones
```

### Tabla `turno_bomba_datos`
```sql
-- Lecturas específicas por bomba
bomba_id, turno_id, user_id
galonaje_super, galonaje_regular, galonaje_diesel
lectura_cc, fotografia, observaciones
fecha_turno, created_at, updated_at
```

### Relaciones Eloquent
```php
// Modelo Turno
public function gasolinera() { return $this->belongsTo(Gasolinera::class); }
public function user() { return $this->belongsTo(User::class); }
public function bombaDatos() { return $this->hasMany(TurnoBombaDatos::class); }
```

## 🎯 Gasolineras y Configuración

### Distribución Actual
- **Gasolinera 4**: Usuario 10, Bombas 13-16
- **Gasolinera 5**: Usuario 11, Bombas 17-20
- **Gasolinera 6**: Usuario 12, Bombas 21-24
- **Gasolinera 7**: Usuario 13, Bombas 25-28

### Configuración de Bombas
- **Bombas 1-3**: Combustibles activos (Super, Regular, Diesel)
- **Bomba 4**: Solo contador de dinero (CC), combustibles en 0

## 🔧 Comandos de Gestión

### Generación de Datos Históricos
```bash
php artisan generate:turnos
```

**Funcionalidad**:
- Genera 3 meses de datos históricos
- 1 turno por día por gasolinera
- Variaciones realistas en ventas (±30%)
- Progresión lógica en lecturas de bombas
- 10-60 galones de incremento diario por bomba

### Migraciones Principales
```bash
# Migración inicial de turnos
php artisan migrate --path=database/migrations/2025_09_13_204714_add_ventas_and_tanques_to_turnos_table.php

# Ejecutar todas las migraciones
php artisan migrate
```

## 📱 PWA (Progressive Web App)

### Configuración
- **Manifest**: `public/manifest.json`
- **Service Worker**: `public/sw.js`
- **Modo Fullscreen**: Implementado para dispositivos móviles
- **Instalación**: Disponible desde navegador

### Características PWA
- Funcionamiento offline limitado
- Instalación en dispositivos móviles
- Pantalla completa sin barra de navegación
- Carga optimizada de recursos

## 🚀 Instalación y Despliegue

### Requisitos
```bash
PHP 8.2+
Composer 2.0+
Node.js 18+
MySQL 8.0+
```

### Instalación Local
```bash
# Clonar repositorio
git clone [repository-url]
cd my-laravel-app

# Instalar dependencias
composer install
npm install

# Configurar ambiente
cp .env.example .env
php artisan key:generate

# Configurar base de datos
php artisan migrate
php artisan db:seed

# Compilar assets
npm run build

# Servidor de desarrollo
php artisan serve
```

### Configuración de Producción
```bash
# Optimizaciones Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permisos de storage
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

## 📂 Estructura de Archivos Clave

### Controllers
- `app/Http/Controllers/TurnosLoginController.php`: Lógica del panel de operador
  - `guardarVentas()`: Procesa totales de ventas
  - `guardarTanques()`: Procesa niveles de tanques
  - `panel()`: Vista principal del operador

### Models
- `app/Models/Turno.php`: Modelo principal de turnos
- `app/Models/TurnoBombaDatos.php`: Modelo de lecturas de bombas
- `app/Models/Gasolinera.php`: Modelo de gasolineras

### Views
- `resources/views/turnos/panel.blade.php`: Panel principal del operador
- `resources/views/turnos/login.blade.php`: Login de operadores

### Filament Resources
- `app/Filament/Resources/TurnoResource.php`: Recurso administrativo principal
- `app/Filament/Resources/TurnoResource/RelationManagers/TurnoBombaDatosRelationManager.php`

### Commands
- `app/Console/Commands/GenerateTurnos.php`: Generador de datos históricos

### Migrations
- `database/migrations/2025_09_13_204714_add_ventas_and_tanques_to_turnos_table.php`

## 🔐 Seguridad y Validaciones

### Validaciones de Formularios
```php
// Ventas
'venta_credito' => 'nullable|numeric|min:0'
'venta_tarjetas' => 'nullable|numeric|min:0'
'venta_efectivo' => 'nullable|numeric|min:0'
'venta_descuentos' => 'nullable|numeric|min:0'

// Tanques
'tanque_*_pulgadas' => 'nullable|numeric|min:0|max:100'
'tanque_*_galones' => 'nullable|numeric|min:0|max:10000'
```

### Compresión de Imágenes
- Automática en subida de fotografías
- Optimización para dispositivos móviles
- Almacenamiento en `storage/app/public/turnos/bombas/`

## 📈 Datos de Ejemplo

### Valores Base Generados
```php
// Ventas típicas
venta_credito: 3,030.00
venta_tarjetas: 225.00
venta_efectivo: 12,776.00
venta_descuentos: 0.00

// Tanques típicos
super_pulgadas: 8.50, super_galones: 266.00
regular_pulgadas: 14.00, regular_galones: 540.00
diesel_pulgadas: 13.25, diesel_galones: 430.00
```

## 🧪 Testing y Debugging

### Logs de Laravel
```bash
# Verificar logs
tail -f storage/logs/laravel.log

# Limpiar logs
> storage/logs/laravel.log
```

### Debugging de Filament
```php
// En TurnoResource.php para debug
protected static bool $shouldSkipAuthorization = true;
```

## 📋 Roadmap y Mejoras Futuras

### Próximas Funcionalidades
- [ ] Reportes automáticos por email
- [ ] Dashboard con gráficos en tiempo real
- [ ] API REST para integraciones externas
- [ ] Notificaciones push para PWA
- [ ] Backup automático de base de datos
- [ ] Sistema de roles y permisos más granular

### Optimizaciones Técnicas
- [ ] Implementar Redis para caché
- [ ] Queue jobs para procesamiento pesado
- [ ] Optimización de consultas con índices
- [ ] Implementar tests unitarios y de integración

## 🤝 Contribución y Mantenimiento

### Commits Recomendados
```bash
git commit -m "feat: nueva funcionalidad de X"
git commit -m "fix: corrección en módulo Y"
git commit -m "docs: actualización de documentación"
git commit -m "refactor: mejora en estructura de Z"
```

### Backup de Base de Datos
```bash
# Generar backup
mysqldump -u user -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql

# Restaurar backup
mysql -u user -p database_name < backup_file.sql
```

---

**Última actualización**: Septiembre 2025
**Versión del sistema**: 2.1.0
**Desarrollado por**: [Tu nombre/empresa]

Para soporte técnico o consultas, revisar este documento completo que contiene toda la información técnica y funcional del sistema.
