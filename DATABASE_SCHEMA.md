# Estructura de Base de Datos - Sistema de Gasolineras

## Esquema General

El sistema utiliza MySQL 8.0 con las siguientes tablas principales y sus relaciones:

```
gasolineras (1) ----< turnos (N)
users (1) ----< turnos (N)
turnos (1) ----< turno_bomba_datos (N)
bombas (1) ----< turno_bomba_datos (N)
gasolineras (1) ----< bombas (N)
```

## Tablas Principales

### 1. `gasolineras`
Almacena información de cada estación de servicio.

| Campo | Tipo | Descripción | Constraints |
|-------|------|-------------|-------------|
| id | INT AUTO_INCREMENT | ID único | PRIMARY KEY |
| nombre | VARCHAR(255) | Nombre de la gasolinera | NOT NULL |
| direccion | TEXT | Dirección física | NULLABLE |
| telefono | VARCHAR(20) | Teléfono de contacto | NULLABLE |
| responsable | VARCHAR(255) | Encargado de la estación | NULLABLE |
| estado | ENUM('activa','inactiva') | Estado operativo | DEFAULT 'activa' |
| created_at | TIMESTAMP | Fecha de creación | AUTO |
| updated_at | TIMESTAMP | Fecha de actualización | AUTO |

### 2. `users`
Usuarios del sistema (administradores y operadores).

| Campo | Tipo | Descripción | Constraints |
|-------|------|-------------|-------------|
| id | INT AUTO_INCREMENT | ID único | PRIMARY KEY |
| name | VARCHAR(255) | Nombre completo | NOT NULL |
| email | VARCHAR(255) | Email único | UNIQUE, NOT NULL |
| password | VARCHAR(255) | Contraseña hasheada | NOT NULL |
| role | ENUM('admin','operador') | Rol en el sistema | DEFAULT 'operador' |
| gasolinera_id | INT | Gasolinera asignada | FOREIGN KEY, NULLABLE |
| estado | ENUM('activo','inactivo') | Estado del usuario | DEFAULT 'activo' |
| email_verified_at | TIMESTAMP | Verificación de email | NULLABLE |
| remember_token | VARCHAR(100) | Token de sesión | NULLABLE |
| created_at | TIMESTAMP | Fecha de creación | AUTO |
| updated_at | TIMESTAMP | Fecha de actualización | AUTO |

### 3. `turnos` ⭐ Tabla Principal
Registros de turnos de trabajo por gasolinera.

| Campo | Tipo | Descripción | Constraints |
|-------|------|-------------|-------------|
| id | INT AUTO_INCREMENT | ID único | PRIMARY KEY |
| gasolinera_id | INT | Gasolinera asociada | FOREIGN KEY, NOT NULL |
| user_id | INT | Operador del turno | FOREIGN KEY, NOT NULL |
| fecha | DATE | Fecha del turno | NOT NULL |
| hora_inicio | DATETIME | Inicio del turno | NULLABLE |
| hora_fin | DATETIME | Final del turno | NULLABLE |
| dinero_apertura | DECIMAL(10,2) | Dinero inicial | DEFAULT 0.00 |
| dinero_cierre | DECIMAL(10,2) | Dinero final | DEFAULT 0.00 |
| estado | ENUM('abierto','cerrado','pausado') | Estado actual | DEFAULT 'abierto' |

#### Campos de Ventas (Agregados 2025-09-13)
| Campo | Tipo | Descripción | Constraints |
|-------|------|-------------|-------------|
| venta_credito | DECIMAL(10,2) | Ventas a crédito | NULLABLE, DEFAULT 0.00 |
| venta_tarjetas | DECIMAL(10,2) | Ventas con tarjetas | NULLABLE, DEFAULT 0.00 |
| venta_efectivo | DECIMAL(10,2) | Ventas en efectivo | NULLABLE, DEFAULT 0.00 |
| venta_descuentos | DECIMAL(10,2) | Descuentos aplicados | NULLABLE, DEFAULT 0.00 |

#### Campos de Tanques (Agregados 2025-09-13)
| Campo | Tipo | Descripción | Constraints |
|-------|------|-------------|-------------|
| tanque_super_pulgadas | DECIMAL(5,2) | Nivel Super en pulgadas | NULLABLE |
| tanque_regular_pulgadas | DECIMAL(5,2) | Nivel Regular en pulgadas | NULLABLE |
| tanque_diesel_pulgadas | DECIMAL(5,2) | Nivel Diesel en pulgadas | NULLABLE |
| tanque_super_galones | DECIMAL(8,2) | Nivel Super en galones | NULLABLE |
| tanque_regular_galones | DECIMAL(8,2) | Nivel Regular en galones | NULLABLE |
| tanque_diesel_galones | DECIMAL(8,2) | Nivel Diesel en galones | NULLABLE |

#### Campos del Sistema
| Campo | Tipo | Descripción | Constraints |
|-------|------|-------------|-------------|
| created_at | TIMESTAMP | Fecha de creación | AUTO |
| updated_at | TIMESTAMP | Fecha de actualización | AUTO |

### 4. `bombas`
Información de bombas por gasolinera.

| Campo | Tipo | Descripción | Constraints |
|-------|------|-------------|-------------|
| id | INT AUTO_INCREMENT | ID único | PRIMARY KEY |
| gasolinera_id | INT | Gasolinera propietaria | FOREIGN KEY, NOT NULL |
| nombre | VARCHAR(100) | Identificador de bomba | NOT NULL |
| numero | INT | Número de bomba | NOT NULL |
| estado | ENUM('activa','inactiva','mantenimiento') | Estado operativo | DEFAULT 'activa' |
| tipo_combustible | JSON | Combustibles disponibles | NULLABLE |
| created_at | TIMESTAMP | Fecha de creación | AUTO |
| updated_at | TIMESTAMP | Fecha de actualización | AUTO |

### 5. `turno_bomba_datos` ⭐ Tabla de Lecturas
Lecturas específicas de bombas por turno.

| Campo | Tipo | Descripción | Constraints |
|-------|------|-------------|-------------|
| id | INT AUTO_INCREMENT | ID único | PRIMARY KEY |
| bomba_id | INT | Bomba leída | FOREIGN KEY, NOT NULL |
| turno_id | INT | Turno asociado | FOREIGN KEY, NOT NULL |
| user_id | INT | Usuario que registra | FOREIGN KEY, NOT NULL |
| galonaje_super | DECIMAL(10,3) | Lectura Super | NULLABLE, DEFAULT 0.000 |
| galonaje_regular | DECIMAL(10,3) | Lectura Regular | NULLABLE, DEFAULT 0.000 |
| galonaje_diesel | DECIMAL(10,3) | Lectura Diesel | NULLABLE, DEFAULT 0.000 |
| lectura_cc | DECIMAL(12,3) | Contador de dinero | NULLABLE, DEFAULT 0.000 |
| fotografia | VARCHAR(500) | Ruta de la imagen | NULLABLE |
| observaciones | TEXT | Notas adicionales | NULLABLE |
| fecha_turno | DATE | Fecha del turno | NOT NULL |
| created_at | TIMESTAMP | Fecha de creación | AUTO |
| updated_at | TIMESTAMP | Fecha de actualización | AUTO |

## Índices y Optimizaciones

### Índices Principales
```sql
-- Índices de rendimiento
CREATE INDEX idx_turnos_gasolinera_fecha ON turnos(gasolinera_id, fecha);
CREATE INDEX idx_turnos_user_fecha ON turnos(user_id, fecha);
CREATE INDEX idx_turno_bomba_datos_turno ON turno_bomba_datos(turno_id);
CREATE INDEX idx_turno_bomba_datos_bomba ON turno_bomba_datos(bomba_id);
CREATE INDEX idx_turno_bomba_datos_fecha ON turno_bomba_datos(fecha_turno);

-- Índices para búsquedas del admin
CREATE INDEX idx_turnos_estado ON turnos(estado);
CREATE INDEX idx_turnos_fecha_estado ON turnos(fecha, estado);
```

### Constraints de Integridad
```sql
-- Foreign Keys principales
ALTER TABLE turnos ADD CONSTRAINT fk_turnos_gasolinera
    FOREIGN KEY (gasolinera_id) REFERENCES gasolineras(id);

ALTER TABLE turnos ADD CONSTRAINT fk_turnos_user
    FOREIGN KEY (user_id) REFERENCES users(id);

ALTER TABLE turno_bomba_datos ADD CONSTRAINT fk_tbd_bomba
    FOREIGN KEY (bomba_id) REFERENCES bombas(id);

ALTER TABLE turno_bomba_datos ADD CONSTRAINT fk_tbd_turno
    FOREIGN KEY (turno_id) REFERENCES turnos(id) ON DELETE CASCADE;

ALTER TABLE bombas ADD CONSTRAINT fk_bombas_gasolinera
    FOREIGN KEY (gasolinera_id) REFERENCES gasolineras(id);
```

## Configuración Actual del Sistema

### Gasolineras Activas
| ID | Nombre | Usuario Asignado | Bombas |
|----|--------|------------------|---------|
| 4 | Gasolinera 4 | 10 | 13, 14, 15, 16 |
| 5 | Gasolinera 5 | 11 | 17, 18, 19, 20 |
| 6 | Gasolinera 6 | 12 | 21, 22, 23, 24 |
| 7 | Gasolinera 7 | 13 | 25, 26, 27, 28 |

### Distribución de Bombas
- **Bombas 1-3 por gasolinera**: Combustibles completos (Super, Regular, Diesel)
- **Bomba 4 por gasolinera**: Solo contador CC, combustibles en 0

### Rangos de Datos Típicos
```sql
-- Ventas diarias típicas por gasolinera
venta_credito: 2,000 - 4,000 Q
venta_tarjetas: 150 - 300 Q
venta_efectivo: 8,000 - 18,000 Q
venta_descuentos: 0 - 500 Q

-- Niveles de tanques típicos
pulgadas: 5.0 - 20.0"
galones: 200 - 800 gal

-- Lecturas de bombas (incremento diario)
galonaje: +10 a +60 galones/día
lectura_cc: +50 a +200 Q/día
```

## Consultas Comunes

### 1. Turnos por Gasolinera en Rango de Fechas
```sql
SELECT t.*, g.nombre as gasolinera, u.name as operador
FROM turnos t
JOIN gasolineras g ON t.gasolinera_id = g.id
JOIN users u ON t.user_id = u.id
WHERE t.gasolinera_id = 4
AND t.fecha BETWEEN '2025-06-01' AND '2025-09-01'
ORDER BY t.fecha DESC;
```

### 2. Lecturas de Bombas por Turno
```sql
SELECT tbd.*, b.nombre as bomba
FROM turno_bomba_datos tbd
JOIN bombas b ON tbd.bomba_id = b.id
WHERE tbd.turno_id = 305
ORDER BY b.numero;
```

### 3. Resumen de Ventas por Gasolinera
```sql
SELECT
    g.nombre,
    SUM(t.venta_credito) as total_credito,
    SUM(t.venta_tarjetas) as total_tarjetas,
    SUM(t.venta_efectivo) as total_efectivo,
    SUM(t.venta_descuentos) as total_descuentos,
    COUNT(*) as total_turnos
FROM turnos t
JOIN gasolineras g ON t.gasolinera_id = g.id
WHERE t.fecha >= CURDATE() - INTERVAL 30 DAY
GROUP BY g.id, g.nombre
ORDER BY total_efectivo DESC;
```

### 4. Progresión de Lecturas de Bomba
```sql
SELECT
    DATE(tbd.fecha_turno) as fecha,
    tbd.galonaje_super,
    tbd.galonaje_regular,
    tbd.galonaje_diesel,
    tbd.lectura_cc
FROM turno_bomba_datos tbd
WHERE tbd.bomba_id = 13
ORDER BY tbd.fecha_turno DESC
LIMIT 30;
```

## Migraciones Ejecutadas

### Migración Principal: `2025_09_13_204714_add_ventas_and_tanques_to_turnos_table.php`
```php
Schema::table('turnos', function (Blueprint $table) {
    // Campos de ventas
    $table->decimal('venta_credito', 10, 2)->nullable()->default(0.00);
    $table->decimal('venta_tarjetas', 10, 2)->nullable()->default(0.00);
    $table->decimal('venta_efectivo', 10, 2)->nullable()->default(0.00);
    $table->decimal('venta_descuentos', 10, 2)->nullable()->default(0.00);

    // Campos de tanques - Pulgadas
    $table->decimal('tanque_super_pulgadas', 5, 2)->nullable();
    $table->decimal('tanque_regular_pulgadas', 5, 2)->nullable();
    $table->decimal('tanque_diesel_pulgadas', 5, 2)->nullable();

    // Campos de tanques - Galones
    $table->decimal('tanque_super_galones', 8, 2)->nullable();
    $table->decimal('tanque_regular_galones', 8, 2)->nullable();
    $table->decimal('tanque_diesel_galones', 8, 2)->nullable();
});
```

## Consideraciones de Rendimiento

### Consultas Optimizadas
- Uso de índices compuestos para búsquedas frecuentes
- Paginación en el panel administrativo
- Carga lazy de relaciones en Eloquent

### Almacenamiento
- Imágenes comprimidas automáticamente
- Backup diario de base de datos
- Rotación de logs de Laravel

### Escalabilidad
- Preparado para múltiples gasolineras
- Soporte para más tipos de combustible
- Extensible para nuevos campos de reporte

---

**Última actualización**: Septiembre 2025
**Versión de esquema**: 2.1.0