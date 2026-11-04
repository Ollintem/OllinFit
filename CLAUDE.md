# OllinFit — Memoria central del sistema

Sistema de administración de gimnasio: socios y membresías, control de acceso por QR, punto de venta (POS) e inventario, flujo de caja por turnos, gastos/compras a proveedores, reportes financieros y dashboard interactivo.

> Convención del proyecto: **rutas, vistas y mensajes en español** (`socios`, `gastos`, `caja`, `planes`…), **modelos y tablas en inglés** (`Member`, `Expense`, `CashRegister`…). Moneda: pesos (MXN). Métodos de pago: `Efectivo` / `Tarjeta` (se guardan con `ucfirst`).

---

## 1. Stack tecnológico

| Capa | Tecnología |
|---|---|
| Backend | PHP 8.3, **Laravel 13** |
| Auth | Laravel Breeze (Blade) |
| Roles / permisos | `spatie/laravel-permission` 8 (RBAC; trait `HasRoles` en `User`) |
| QR | `simplesoftwareio/simple-qrcode` (gafete del socio) + `html5-qrcode` (escáner en `acceso/index`) |
| Imágenes | `intervention/image-laravel` + conversión a WebP con GD (`ProductController::convertirYGuardarWebp`) |
| Frontend | Blade + **Tailwind CSS 3** (Vite y también CDN `cdn.tailwindcss.com` en vistas) + **Alpine.js 3** |
| Gráficas / UI | Chart.js (CDN), SweetAlert2 (CDN) |
| Base de datos | SQLite por defecto en `.env.example` (compatible con MySQL) |
| Tests | PHPUnit 12 (solo tests de Breeze por ahora) |

Comandos: `composer setup` (instalación completa), `composer dev` (servidor + Vite), `composer test`, `npm run build`.

---

## 2. Arquitectura y base de datos

### Tablas principales y relaciones

**Membresías / Socios**
- `plans` — `name`, `price`, `duration_days`, `description`, `highlight_text`, `color`, `is_active`. `Plan hasMany Member`.
- `members` — `folio` (único, formato `MX-9xxx` = id + 9000), `name`, `last_name`, `email`, `phone`, `profile_photo_path`, `plan_id` (FK → plans, *nullOnDelete*), `expiration_date`, `is_active`, `is_inside` (está dentro del gym). Accessor `status` en el modelo.
- `payments` — `member_id` (FK, cascade), `plan_name`, `amount`, `payment_method`, `folio_pago` (único, `TXN-…`). Historial de pagos de membresía.
- `access_logs` — `member_id` (FK, cascade), `access_method` (`Escáner QR` / `Liberación Manual`), `status` (`Entrada` / `Salida` / `Denegado`).
- `memberships` — `member_id`, `plan_id`, `start_date`, `end_date`. **Existe pero no se usa** (la vigencia vive en `members.expiration_date`).

**Productos / Ventas (POS)**
- `products` — `name`, `description`, `price`, `is_service` (pases/servicios sin inventario), `stock`, `min_stock`, `image_path`.
- `sales` — `user_id` (quién cobró), `total`, `payment_method`. `Sale hasMany SaleItem`.
- `sale_items` — `sale_id` (cascade), `product_id` (cascade), `quantity`, `price`.

**Caja**
- `cash_registers` — `user_id` (quién abrió), `opening_amount`, `closing_amount` (contado), `expected_amount` (calculado), `difference` (sobrante +/faltante −), `status` (`abierta`|`cerrada`), `opened_at`, `closed_at`.

**Gastos / Proveedores**
- `suppliers` — `name`, `contact_name`, `phone`, `email`. `Supplier hasMany Expense`.
- `expenses` — `type` (`compra_proveedor`|`gasto_general`), `concept`, `description`, `amount`, `payment_method`, `supplier_id` (nullOnDelete), `cash_register_id` (turno del que salió el dinero, nullOnDelete), `user_id`.
- `expense_items` — `expense_id` (cascade), `product_id`, `quantity`, `unit_cost`, `subtotal` (detalle de compras de inventario).

**Usuarios / Seguridad**
- `users` (empleados) + tablas de Spatie (`roles` con columna extra `description`, `permissions`, pivotes).

### Diagrama de relaciones (resumen)
```
plans 1─* members 1─* payments
                  1─* access_logs
products 1─* sale_items *─1 sales *─1 users
products 1─* expense_items *─1 expenses *─1 suppliers
                                 expenses *─1 cash_registers *─1 users
```

### Capas de la app
- **Controladores** (`app/Http/Controllers`): `Dashboard`, `Member` (socios + renovaciones), `Access` (QR), `Plan`, `Product`, `Sale` (POS), `CashRegister` (caja), `Expense` (gastos), `Supplier`, `Report`, `Employee`, `Role`, `Profile`.
- **Middleware** (`app/Http/Middleware`):
  - `CheckCashRegisterOpen` — bloquea rutas de cobro si no hay caja abierta (redirige a `caja.index`).
  - `CheckActiveUser` (alias `active`) — **activo**: bloquea el acceso a cuentas con `is_active = false`.
  - Alias Spatie: `role`, `permission`, `role_or_permission` (en `bootstrap/app.php`). El `permission:` protege **todos** los módulos (ver `config/permisos.php`); varias rutas de cobro/acceso además tienen `throttle`.
- **Rutas**: todo en `routes/web.php` dentro de `auth` + `active`. `/` muestra el login.
- **Vistas** (`resources/views`): `socios/`, `acceso/`, `planes/`, `productos/`, `sales/` (POS, ticket, historial), `caja/`, `gastos/`, `proveedores/`, `reports/`, `employees/`, `roles/`, `dashboard.blade.php`, `components/sidebar.blade.php`.
- La lógica de negocio vive directamente en los controladores (no hay Services/Actions ni Form Requests).

---

## 3. Estado actual — módulos terminados y funcionando

- **Autenticación** (Breeze) y perfil de usuario.
- **Empleados y Roles (RBAC)**: CRUD de empleados con foto WebP; roles con matriz de permisos por módulo (fuente única: `config/permisos.php`); **permisos por empleado** (otorgar/denegar sobre el rol, solo Administrador — ver reglas 20-21). Protecciones anti-escalamiento en empleados.
- **Planes**: CRUD completo con conteo de socios por plan.
- **Socios**: alta con cobro y ticket automático, listado con búsqueda/filtros (activos, vencidos, inactivos, por plan) y KPIs, perfil con gafete QR y barra de progreso de vigencia, renovación por modal (JSON), edición con foto, historial de pagos y de accesos.
- **Control de acceso QR**: escaneo vía AJAX con lógica *toggle* (entra/sale), rechazo por membresía vencida, lista de socios adentro y liberación manual.
- **Productos / Inventario**: CRUD, servicios vs. productos físicos, imágenes WebP, reabastecimiento sumando stock.
- **Punto de Venta (POS)**: carrito en frontend (JSON), venta en transacción DB con descuento de stock, ticket imprimible / modal de ticket, historial de ventas con filtros (folio, método, rango de fechas).
- **Flujo de caja**: apertura con fondo inicial, cálculo en vivo (efectivo, tarjeta, egresos), corte con diferencia sobrante/faltante, historial de cortes.
- **Gastos y compras**: gastos generales y compras a proveedor con detalle de productos (entrada de stock), filtros, edición de textos y eliminación con reversión de inventario.
- **Proveedores**: alta, edición, eliminación protegida.
- **Reportes financieros**: reporte diario de ingresos (POS + membresías en `payments`) y egresos, balance neto, efectivo vs. tarjeta, renovaciones próximas; días anteriores solo con `ver_reportes_historicos`; **exportación a PDF** (vista de impresión del navegador).
- **Dashboard interactivo**: estado de caja en vivo, utilidad del mes y crecimiento vs. mes anterior, gráficas filtrables (año/mes/semana), distribución tienda vs. membresías, últimos tickets, accesos de hoy vs. ayer, socios por vencer, stock bajo, top 5 productos.

---

## 4. Reglas de negocio clave

**Caja**
1. **No se puede cobrar con la caja cerrada**: alta de socios, renovaciones, POS y gastos están detrás de `CheckCashRegisterOpen`.
2. Solo puede existir **una caja abierta** a la vez.
3. Efectivo esperado = `opening_amount` + ingresos en efectivo del turno − gastos en efectivo con `cash_register_id` de esa caja.
4. `difference` = efectivo contado − esperado (positivo = sobrante, negativo = faltante).
5. Los pagos con tarjeta se reportan pero **no** afectan el efectivo esperado.

**Gastos**
6. Los gastos **restan de la caja** del turno (se ligan por `cash_register_id`).
7. No se puede registrar un gasto en efectivo sin caja abierta.
8. Una `compra_proveedor` **incrementa el stock** de cada producto; eliminar el gasto **revierte** el stock y borra el detalle.
9. Un gasto solo permite editar textos (concepto/descripción); si el monto está mal, se elimina y se registra de nuevo.
10. No se puede eliminar un proveedor que ya tiene compras registradas.

**Socios y membresías**
11. En pagos en efectivo, el monto recibido debe ser **≥ precio del plan** (si no, se rechaza el cobro).
12. Vencimiento = fecha base extendida por la **duración del plan** (`duration_value` + `duration_unit`: días/meses/años, por calendario real con `Plan::extend()`). Si se renueva **antes de vencer**, se extiende desde la fecha de vencimiento actual.
13. Un socio con pagos/accesos registrados **no se elimina**: se marca como inactivo.
14. El folio se genera en el backend (`MX-` + id + 9000).

**Acceso**
15. Membresía vencida ⇒ acceso **denegado** (se registra en `access_logs` como `Denegado`).
16. Escaneo válido alterna `is_inside` (Entrada ↔ Salida) y registra el log.

**Inventario / POS**
17. Los servicios (`is_service`) no manejan stock ni imagen.
18. Cada venta descuenta stock de los productos físicos (precio tomado de la BD; rechaza venta sin stock).
19. Dashboard: stock bajo = entre 1 y 5 piezas; "por vencer" = vence en los próximos 5 días; "Accesos Hoy" cuenta solo `status = 'Entrada'`.

**Seguridad / permisos**
20. **Permisos por empleado** (override del rol): `efectivo = (permisos del rol ∪ directos) − denegados`. Permite dar accesos extra o quitarle uno a un empleado sin tocar el rol ni afectar a otros. `User::hasPermissionTo` respeta las denegaciones (tabla `denied_permissions`). El panel vive en editar empleado y **solo lo configura el Administrador**.
21. El rol **Administrador** pasa todas las verificaciones (`Gate::before`). Solo un Administrador puede crear/editar/eliminar a otro Administrador, asignar ese rol, o configurar permisos por empleado; no es posible auto-eliminarse ni borrar al último Administrador.

---

## 5. Deuda técnica y bugs conocidos

**Resuelto recientemente** (ya no es deuda, no reintroducir):
- Doble conteo de ingresos → una sola fuente: membresías/renovaciones en `payments`, POS en `sales`. Caja, reportes y dashboard ajustados.
- Dashboard "caja en vivo" usa `opening_amount` + `opened_at`/`cash_register_id` (ya no `initial_amount`/`created_at`).
- POS valida en servidor: stock suficiente, **precio desde la BD** (no del cliente), cantidades `>= 1`, `user_id` = usuario autenticado.
- `CheckActiveUser` reactivado; `permission:` aplicado a **todos** los módulos (`config/permisos.php` es la fuente única).
- Renovación unificada: se eliminó la ruta duplicada `socios.renew`; la vigencia se calcula con `Plan::extend()` (no por nombre de plan).
- `ReportController::exportarPdf` implementado (renderiza `reports/pdf.blade.php`, imprime/guarda como PDF).
- Dashboard "Accesos Hoy" cuenta solo **entradas**; % vs ayer honesto.
- Seguridad: XSS en listados neutralizado (`data-*`/`@js`/`textContent`), escalada en empleados bloqueada, `throttle` en cobro/acceso, el escaneo QR ya no expone datos del socio, ticket armado desde el `Payment` guardado.
- Permisos por empleado (override del rol): permisos directos + denegados (`denied_permissions`), ver regla 20.

**Pendiente / a vigilar:**
- Tabla `memberships` sin uso (la vigencia vive en `members.expiration_date`).
- Folio (`MX-` + id + 9000) susceptible a condiciones de carrera con alta concurrencia.
- `throttle` comparte cupo por usuario entre rutas (no es independiente por función); ver *named limiters* si se quiere afinar.
- Autorización por módulo **sin ownership**: con el permiso del módulo, un empleado ve recursos de todos (p. ej. cortes de otros). Aceptable para una sucursal; decidir si filtrar por usuario.
- Tailwind por CDN aún en `socios/ticket.blade.php` (vista de impresión).
- Caja global única (no hay caja por usuario/turno).
- Sin tests de features del dominio (solo tests de Breeze).

---

## 6. Próximos pasos

1. **Tests de features** del dominio (caja, POS, gastos, renovaciones, RBAC y denegaciones por empleado).
2. **Decisiones de diseño**: usar o eliminar `memberships`; adoptar Livewire o documentar que Alpine cumple ese rol; *ownership* por usuario en ventas/cortes.
3. **Named limiters** de `throttle` independientes por función (POS vs escáner).
4. **Recordatorios automáticos** de vencimiento a socios (email/WhatsApp).
5. **Cajas por usuario/turno** (hoy es una caja global).
6. Endurecer la generación de **folio** ante concurrencia.
7. **Despliegue** a producción (VPS Ubuntu + Nginx + MySQL): PHP 8.3, `php-gd`, `storage:link`, `.env` de producción (`APP_DEBUG=false`, DB MySQL, SMTP real), `optimize`.
