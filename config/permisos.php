<?php

/*
|--------------------------------------------------------------------------
| Matriz de permisos de OllinFit
|--------------------------------------------------------------------------
| Fuente única de verdad del RBAC. De aquí se alimentan:
|   - El seeder RolePermissionSeeder (crea/poda los permisos).
|   - La matriz de la pantalla de roles (create/edit).
|   - La referencia de qué permiso protege cada ruta (routes/web.php).
|
| Para agregar un permiso nuevo: añádelo aquí y protege su ruta. El seeder
| y la UI se actualizan solos.
|
| 'solo_admin' => true marca un módulo cuyas rutas, además del permiso,
| exigen el rol Administrador (defensa extra para la gestión de roles).
*/

return [

    'dashboard' => [
        'label' => 'Dashboard',
        'permisos' => [
            'ver_dashboard' => 'Ver el panel',
        ],
    ],

    'socios' => [
        'label' => 'Socios',
        'permisos' => [
            'ver_socios'      => 'Ver',
            'crear_socios'    => 'Crear',
            'editar_socios'   => 'Editar',
            'renovar_socios'  => 'Renovar membresía',
            'eliminar_socios' => 'Eliminar',
        ],
    ],

    'planes' => [
        'label' => 'Planes',
        'permisos' => [
            'ver_planes'      => 'Ver',
            'crear_planes'    => 'Crear',
            'editar_planes'   => 'Editar',
            'eliminar_planes' => 'Eliminar',
        ],
    ],

    'pos' => [
        'label' => 'Punto de Venta',
        'permisos' => [
            'usar_pos'             => 'Vender',
            'ver_historial_ventas' => 'Ver historial de ventas',
        ],
    ],

    'caja' => [
        'label' => 'Caja',
        'permisos' => [
            'ver_caja'    => 'Ver flujo y cortes',
            'operar_caja' => 'Abrir / cerrar caja',
        ],
    ],

    'gastos' => [
        'label' => 'Compras y Gastos',
        'permisos' => [
            'ver_gastos'      => 'Ver',
            'crear_gastos'    => 'Crear',
            'editar_gastos'   => 'Editar',
            'eliminar_gastos' => 'Eliminar',
        ],
    ],

    'productos' => [
        'label' => 'Inventario y Artículos',
        'permisos' => [
            'ver_productos'      => 'Ver',
            'crear_productos'    => 'Crear',
            'editar_productos'   => 'Editar',
            'eliminar_productos' => 'Eliminar',
        ],
    ],

    'proveedores' => [
        'label' => 'Proveedores',
        'permisos' => [
            'ver_proveedores'      => 'Ver',
            'crear_proveedores'    => 'Crear',
            'editar_proveedores'   => 'Editar',
            'eliminar_proveedores' => 'Eliminar',
        ],
    ],

    'acceso' => [
        'label' => 'Control de Acceso',
        'permisos' => [
            'ver_acceso'    => 'Ver pantalla y socios dentro',
            'operar_acceso' => 'Escanear / liberar',
        ],
    ],

    'reportes' => [
        'label' => 'Reportes',
        'permisos' => [
            'ver_reportes'      => 'Ver',
            'exportar_reportes' => 'Exportar',
        ],
    ],

    'empleados' => [
        'label' => 'Empleados',
        'permisos' => [
            'ver_empleados'      => 'Ver',
            'crear_empleados'    => 'Crear',
            'editar_empleados'   => 'Editar',
            'eliminar_empleados' => 'Eliminar',
        ],
    ],

    'roles' => [
        'label' => 'Roles y Permisos',
        'solo_admin' => true,
        'permisos' => [
            'gestionar_roles' => 'Gestionar roles y permisos (solo Administrador)',
        ],
    ],

];
