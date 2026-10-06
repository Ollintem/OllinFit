<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Financiero - OllinFit</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
            font-size: 13px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #f97316;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .logo h1 {
            margin: 0;
            color: #1e293b;
            font-size: 24px;
        }
        .logo p {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .info-reporte {
            text-align: right;
        }
        .info-reporte p {
            margin: 2px 0;
            color: #64748b;
            font-size: 11px;
        }
        .resumen-box {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }
        .card {
            flex: 1;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
        }
        .card h3 {
            margin: 0 0 5px;
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
        }
        .card .valor {
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
        }
        .card .sub {
            font-size: 11px;
            color: #059669;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #1e293b;
            color: white;
            text-align: left;
            padding: 10px;
            font-size: 11px;
            text-transform: uppercase;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <!-- Encabezado del Reporte -->
    <div class="header">
        <div class="logo">
            <h1>OllinFit</h1>
            <p>Sistema de Gestión de Gimnasio</p>
        </div>
        <div class="info-reporte">
            <p><strong>Corte Diario / Financiero</strong></p>
            <p>Fecha de consulta: <strong>{{ $fechaSeleccionada }}</strong></p>
            <p>Generado: {{ date('d/m/Y H:i') }}</p>
        </div>
    </div>

    <!-- Tarjetas de Resumen Ejecutivo -->
    <div class="resumen-box">
        <div class="card">
            <h3>Total Ingresos del Día</h3>
            <div class="valor" style="color: #059669;">${{ number_format($totalIngresos, 2) }} <span style="font-size: 12px; color: #64748b;">MXN</span></div>
            <div class="sub">Corte Cuadrado</div>
        </div>
        <div class="card">
            <h3>Desglose de Efectivo</h3>
            <div class="valor">${{ number_format($efectivo, 2) }}</div>
            <div class="sub">Ventas físicas en caja</div>
        </div>
        <div class="card">
            <h3>Desglose con Tarjeta</h3>
            <div class="valor">${{ number_format($tarjeta, 2) }}</div>
            <div class="sub">Terminal / Electrónico</div>
        </div>
        <div class="card">
            <h3>Total Transacciones</h3>
            <div class="valor">{{ count($transacciones) }}</div>
            <div class="sub">Operaciones registradas</div>
        </div>
    </div>

    <!-- Tabla Detallada de Ingresos -->
    <h3 style="color: #1e293b; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; margin-bottom: 10px;">Detalle de Transacciones e Ingresos</h3>
    <table>
        <thead>
            <tr>
                <th>Hora</th>
                <th>Concepto / ID</th>
                <th>Tipo de Ingreso</th>
                <th>Método de Pago</th>
                <th class="text-right">Monto</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transacciones as $tx)
                <tr>
                    <td>{{ $tx->created_at->format('h:i A') }}</td>
                    <td><strong>{{ $tx->concepto_id ?? '#' . $tx->id }}</strong></td>
                    <td>{{ $tx->origen ?? 'Venta' }}</td>
                    <td>{{ $tx->payment_method ?? 'Efectivo' }}</td>
                    <td class="text-right" style="font-weight: bold; color: #059669;">${{ number_format($tx->monto, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #94a3b8; padding: 20px;">
                        No hay transacciones registradas para esta fecha.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>OllinFit Management System — Reporte confidencial para uso administrativo y de control interno.</p>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            window.print();
        });
    </script>
</body>
</html>