<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <?php require_once ROOT . '/app/models/VeterinariaModel.php'; ?>
    <?php $radicado = VeterinariaModel::serialVenta($venta['veterinaria_nombre'], $venta['id'], $venta['created_at']); ?>
    <title>Factura <?= htmlspecialchars($radicado) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 12px;
            color: #1f2937;
            background: #fff;
            padding: 32px 40px;
        }

        .rpt-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 18px;
            border-bottom: 2px solid #4f46e5;
            margin-bottom: 20px;
        }
        .rpt-logo { display: flex; align-items: center; gap: 10px; }
        .rpt-logo-icon {
            width: 42px; height: 42px;
            background: linear-gradient(135deg, #1e1b4b, #4f46e5);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 20px;
        }
        .rpt-logo-name  { font-size: 20px; font-weight: 800; color: #1e1b4b; }
        .rpt-logo-sub   { font-size: 10px; color: #6b7280; letter-spacing: .05em; text-transform: uppercase; }
        .rpt-meta       { text-align: right; }
        .rpt-meta-title { font-size: 16px; font-weight: 700; color: #1e1b4b; }
        .rpt-meta-radicado {
            font-family: monospace; font-size: 13px; font-weight: 700; color: #4f46e5;
            background: #eef2ff; border: 1px solid #c7d2fe; border-radius: .3rem;
            padding: 2px 8px; display: inline-block; margin: 4px 0;
        }
        .rpt-meta-info  { font-size: 10px; color: #9ca3af; }

        .factura-estado {
            display: inline-block;
            font-size: 11px; font-weight: 700; text-transform: uppercase;
            letter-spacing: .05em; border-radius: 4px; padding: 3px 10px;
            margin-bottom: 16px;
        }
        .estado-completada { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .estado-pendiente  { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
        .estado-anulada    { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }
        .info-box {
            border: 1px solid #e8eaf0;
            border-radius: 8px;
            padding: 12px 16px;
        }
        .info-label { font-size: 9px; color: #9ca3af; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 4px; }
        .info-value { font-size: 13px; font-weight: 600; color: #1e1b4b; }
        .info-sub   { font-size: 11px; color: #6b7280; margin-top: 2px; }

        table { width: 100%; border-collapse: collapse; font-size: 11px; margin-bottom: 4px; }
        thead th {
            background: #312e81; color: #fff; padding: 8px 10px; text-align: left;
            font-weight: 600; font-size: 10px; text-transform: uppercase; letter-spacing: .04em;
        }
        thead th.r { text-align: right; }
        thead th.c { text-align: center; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        tbody td { padding: 7px 10px; border-bottom: 1px solid #f3f4f6; }
        tbody td.r { text-align: right; }
        tbody td.c { text-align: center; }

        .totales { width: 260px; margin-left: auto; margin-top: 12px; }
        .totales-row { display: flex; justify-content: space-between; padding: 5px 10px; font-size: 12px; }
        .totales-row.total {
            font-size: 15px; font-weight: 800; color: #4f46e5;
            border-top: 2px solid #4f46e5; margin-top: 4px; padding-top: 8px;
        }

        .rpt-footer {
            margin-top: 28px; padding-top: 12px; border-top: 1px solid #e8eaf0;
            display: flex; justify-content: space-between; font-size: 10px; color: #9ca3af;
        }

        @media print {
            body { padding: 0; }
            @page { margin: 15mm 15mm 12mm 15mm; size: A4; }
        }
    </style>
</head>
<body>

    <div class="rpt-header">
        <div class="rpt-logo">
            <div class="rpt-logo-icon">&#128722;</div>
            <div>
                <div class="rpt-logo-name">StoreControl</div>
                <div class="rpt-logo-sub">Sistema de punto de venta</div>
            </div>
        </div>
        <div class="rpt-meta">
            <div class="rpt-meta-title">Factura de Venta</div>
            <div class="rpt-meta-radicado"><?= htmlspecialchars($radicado) ?></div>
            <div class="rpt-meta-info">
                Generado: <?= date('d/m/Y H:i') ?> &bull; Por: <?= htmlspecialchars($usuario['nombre']) ?>
            </div>
        </div>
    </div>

    <?php
        $estadoLabel = ['completada' => 'Completada', 'pendiente' => 'Pendiente', 'anulada' => 'Cancelada'][$venta['estado']] ?? $venta['estado'];
    ?>
    <span class="factura-estado estado-<?= htmlspecialchars($venta['estado']) ?>"><?= $estadoLabel ?></span>

    <div class="info-grid">
        <div class="info-box">
            <div class="info-label">Cliente</div>
            <div class="info-value"><?= htmlspecialchars(trim($venta['cliente_nombre']) ?: 'Cliente general') ?></div>
            <?php if (!empty($venta['cliente_dni']) || !empty($venta['cliente_telefono'])): ?>
            <div class="info-sub">
                <?= $venta['cliente_dni'] ? 'DNI: ' . htmlspecialchars($venta['cliente_dni']) : '' ?>
                <?= $venta['cliente_dni'] && $venta['cliente_telefono'] ? ' &bull; ' : '' ?>
                <?= $venta['cliente_telefono'] ? htmlspecialchars($venta['cliente_telefono']) : '' ?>
            </div>
            <?php endif; ?>
        </div>
        <div class="info-box">
            <div class="info-label">Sucursal / Vendedor</div>
            <div class="info-value"><?= htmlspecialchars($venta['veterinaria_nombre']) ?></div>
            <div class="info-sub">Atendido por <?= htmlspecialchars($venta['vendedor_nombre'] ?: 'Sistema') ?></div>
            <div class="info-sub"><?= date('d/m/Y H:i', strtotime($venta['created_at'])) ?></div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th class="c" style="width:90px;">Cantidad</th>
                <th class="r" style="width:100px;">P. Unitario</th>
                <th class="r" style="width:90px;">Descuento</th>
                <th class="r" style="width:100px;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($detalles)): ?>
            <tr><td colspan="5" style="text-align:center; padding:20px; color:#9ca3af;">Sin productos.</td></tr>
            <?php else: ?>
            <?php foreach ($detalles as $d): ?>
            <tr>
                <td>
                    <?= htmlspecialchars($d['producto_nombre']) ?>
                    <?php if ($d['codigo']): ?><br><span style="color:#9ca3af;">Cód: <?= htmlspecialchars($d['codigo']) ?></span><?php endif; ?>
                </td>
                <td class="c"><?= $d['cantidad'] ?> <?= htmlspecialchars($d['unidad']) ?></td>
                <td class="r">$<?= number_format($d['precio_unitario'], 2) ?></td>
                <td class="r"><?= $d['descuento'] > 0 ? '-$' . number_format($d['descuento'], 2) : '—' ?></td>
                <td class="r" style="font-weight:700;">$<?= number_format($d['subtotal'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="totales">
        <div class="totales-row">
            <span>Subtotal</span>
            <span>$<?= number_format($venta['subtotal'], 2) ?></span>
        </div>
        <?php if ($venta['descuento'] > 0): ?>
        <div class="totales-row">
            <span>Descuento</span>
            <span>-$<?= number_format($venta['descuento'], 2) ?></span>
        </div>
        <?php endif; ?>
        <div class="totales-row total">
            <span>Total</span>
            <span>$<?= number_format($venta['total'], 2) ?></span>
        </div>
    </div>

    <?php if (!empty($venta['notas'])): ?>
    <div class="info-box" style="margin-top:16px;">
        <div class="info-label">Notas</div>
        <div class="info-sub"><?= nl2br(htmlspecialchars($venta['notas'])) ?></div>
    </div>
    <?php endif; ?>

    <div class="rpt-footer">
        <span>StoreControl &mdash; <?= htmlspecialchars($venta['veterinaria_nombre']) ?></span>
        <span>Documento generado el <?= date('d/m/Y \a \l\a\s H:i') ?></span>
    </div>

</body>
<script>window.onload = function(){ window.print(); }</script>
</html>
