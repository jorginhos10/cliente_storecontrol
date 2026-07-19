<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ingresos — <?= htmlspecialchars($sucursal_nombre) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 12px;
            color: #1f2937;
            background: #fff;
            padding: 32px 40px;
        }

        /* ── Encabezado ── */
        .rpt-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 18px;
            border-bottom: 2px solid #4f46e5;
            margin-bottom: 20px;
        }
        .rpt-logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }
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
        .rpt-meta-suc   { font-size: 12px; color: #4f46e5; font-weight: 600; margin: 2px 0; }
        .rpt-meta-info  { font-size: 10px; color: #9ca3af; }

        /* ── Período ── */
        .rpt-periodo {
            background: #eef2ff;
            border-left: 4px solid #4f46e5;
            border-radius: 0 6px 6px 0;
            padding: 10px 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .rpt-periodo-label { font-size: 10px; font-weight: 700; text-transform: uppercase;
                             letter-spacing: .06em; color: #6b7280; }
        .rpt-periodo-value { font-size: 13px; font-weight: 700; color: #1e1b4b; }

        /* ── Resumen ── */
        .rpt-summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }
        .rpt-box {
            border: 1px solid #e8eaf0;
            border-radius: 8px;
            padding: 14px 16px;
            text-align: center;
        }
        .rpt-box-icon  { font-size: 20px; margin-bottom: 6px; }
        .rpt-box-value { font-size: 22px; font-weight: 800; color: #1e1b4b; line-height: 1.1; }
        .rpt-box-label { font-size: 10px; color: #9ca3af; font-weight: 600;
                         text-transform: uppercase; letter-spacing: .05em; margin-top: 3px; }
        .rpt-box.blue    { border-color: #bfdbfe; background: #eff6ff; }
        .rpt-box.blue    .rpt-box-value { color: #1d4ed8; }
        .rpt-box.orange { border-color: #fed7aa; background: #fff7ed; }
        .rpt-box.orange .rpt-box-value { color: #c2410c; }
        .rpt-box.green   { border-color: #bbf7d0; background: #f0fdf4; }
        .rpt-box.green   .rpt-box-value { color: #16a34a; }

        /* ── Tipos ── */
        .rpt-tipos {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }
        .rpt-tipo-box {
            border: 1px solid #e8eaf0;
            border-radius: 6px;
            padding: 10px 12px;
            text-align: center;
        }
        .rpt-tipo-value { font-size: 18px; font-weight: 800; color: #1e1b4b; }
        .rpt-tipo-label { font-size: 9px; color: #6b7280; text-transform: uppercase;
                            letter-spacing: .05em; margin-top: 2px; }
        .tipo-compra                { border-color: #bbf7d0; background: #f0fdf4; }
        .tipo-transferencia_entrada { border-color: #bae6fd; background: #f0f9ff; }
        .tipo-transferencia_salida  { border-color: #fde68a; background: #fffbeb; }

        /* ── Tabla ── */
        .rpt-table-title {
            font-size: 12px; font-weight: 700; color: #1e1b4b;
            text-transform: uppercase; letter-spacing: .06em;
            margin-bottom: 8px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e8eaf0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        thead th {
            background: #312e81;
            color: #fff;
            padding: 8px 10px;
            text-align: left;
            font-weight: 600;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        thead th.r { text-align: right; }
        thead th.c { text-align: center; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        tbody td { padding: 7px 10px; border-bottom: 1px solid #f3f4f6; }
        tbody td.r { text-align: right; }
        tbody td.c { text-align: center; }
        .badge-tipo {
            border-radius: 4px; padding: 2px 6px; font-size: 10px; font-weight: 700;
        }
        .badge-compra                { background: #f0fdf4; color: #16a34a; }
        .badge-transferencia_entrada { background: #f0f9ff; color: #1d4ed8; }
        .badge-transferencia_salida  { background: #fffbeb; color: #92400e; }
        tfoot td {
            padding: 9px 10px;
            font-weight: 700;
            background: #eef2ff;
            border-top: 2px solid #4f46e5;
            font-size: 12px;
        }
        tfoot td.r { text-align: right; color: #4f46e5; }

        /* ── Pie de página ── */
        .rpt-footer {
            margin-top: 24px;
            padding-top: 12px;
            border-top: 1px solid #e8eaf0;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #9ca3af;
        }

        @media print {
            body { padding: 0; }
            @page { margin: 15mm 15mm 12mm 15mm; size: A4; }
        }
    </style>
</head>
<body>

    <!-- Encabezado -->
    <div class="rpt-header">
        <div class="rpt-logo">
            <div class="rpt-logo-icon">&#128722;</div>
            <div>
                <div class="rpt-logo-name">StoreControl</div>
                <div class="rpt-logo-sub">Sistema de punto de venta</div>
            </div>
        </div>
        <div class="rpt-meta">
            <div class="rpt-meta-title">Reporte de Ingresos</div>
            <div class="rpt-meta-suc"><?= htmlspecialchars($sucursal_nombre) ?></div>
            <div class="rpt-meta-info">Generado: <?= date('d/m/Y H:i') ?> &bull; Por: <?= htmlspecialchars($usuario['nombre']) ?></div>
        </div>
    </div>

    <!-- Período -->
    <div class="rpt-periodo">
        <div>
            <div class="rpt-periodo-label">Período del reporte</div>
            <div class="rpt-periodo-value">
                <?php if ($desde === $hasta): ?>
                    <?= date('d \d\e F \d\e Y', strtotime($desde)) ?>
                <?php else: ?>
                    <?= date('d/m/Y', strtotime($desde)) ?> &nbsp;→&nbsp; <?= date('d/m/Y', strtotime($hasta)) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Resumen general -->
    <div class="rpt-summary">
        <div class="rpt-box blue">
            <div class="rpt-box-icon">&#9633;</div>
            <div class="rpt-box-value"><?= $resumen['total_ingresos'] ?? 0 ?></div>
            <div class="rpt-box-label">Total registros</div>
        </div>
        <div class="rpt-box orange">
            <div class="rpt-box-icon">&#9632;</div>
            <div class="rpt-box-value"><?= number_format($resumen['total_unidades'] ?? 0) ?></div>
            <div class="rpt-box-label">Unidades ingresadas</div>
        </div>
        <div class="rpt-box green">
            <div class="rpt-box-icon">$</div>
            <div class="rpt-box-value">$<?= number_format($resumen['valor_total'] ?? 0, 2) ?></div>
            <div class="rpt-box-label">Valor total</div>
        </div>
    </div>

    <!-- Desglose por tipo -->
    <div class="rpt-tipos">
        <div class="rpt-tipo-box tipo-compra">
            <div class="rpt-tipo-value"><?= $resumen['por_compra'] ?? 0 ?></div>
            <div class="rpt-tipo-label">Compra</div>
        </div>
        <div class="rpt-tipo-box tipo-transferencia_entrada">
            <div class="rpt-tipo-value"><?= $resumen['por_transferencia_entrada'] ?? 0 ?></div>
            <div class="rpt-tipo-label">Transferencia recibida</div>
        </div>
        <div class="rpt-tipo-box tipo-transferencia_salida">
            <div class="rpt-tipo-value"><?= $resumen['por_transferencia_salida'] ?? 0 ?></div>
            <div class="rpt-tipo-label">Transferencia enviada</div>
        </div>
    </div>

    <!-- Detalle -->
    <div class="rpt-table-title">Detalle de ingresos</div>

    <table>
        <thead>
            <tr>
                <th style="width:30px;">#</th>
                <th style="width:120px;">Tipo</th>
                <th>Proveedor</th>
                <th class="c" style="width:60px;">Prods.</th>
                <th class="c" style="width:70px;">Unidades</th>
                <th style="max-width:140px;">Notas</th>
                <th class="r" style="width:90px;">Valor</th>
                <th class="c" style="width:90px;">Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($ingresos)): ?>
            <tr>
                <td colspan="8" style="text-align:center; padding:20px; color:#9ca3af;">
                    No hay ingresos en el período seleccionado.
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($ingresos as $i => $ing): ?>
            <?php $t = $tipos[$ing['tipo']] ?? ['label' => $ing['tipo']]; ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td>
                    <span class="badge-tipo badge-<?= htmlspecialchars($ing['tipo']) ?>">
                        <?= $t['label'] ?>
                    </span>
                </td>
                <td><?= htmlspecialchars($ing['proveedor'] ?: '—') ?></td>
                <td class="c"><?= $ing['total_lineas'] ?></td>
                <td class="c"><?= number_format($ing['total_unidades'] ?? 0) ?></td>
                <td style="max-width:140px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    <?= htmlspecialchars($ing['notas'] ?: '—') ?>
                </td>
                <td class="r" style="font-weight:700;">$<?= number_format($ing['total'], 2) ?></td>
                <td class="c">
                    <?= date('d/m/Y', strtotime($ing['created_at'])) ?><br>
                    <span style="color:#9ca3af;"><?= date('H:i', strtotime($ing['created_at'])) ?></span>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <?php if (!empty($ingresos)): ?>
        <tfoot>
            <tr>
                <td colspan="6" style="text-align:right;">Total del período (<?= $resumen['total_ingresos'] ?? 0 ?> registros)</td>
                <td class="r">$<?= number_format($resumen['valor_total'] ?? 0, 2) ?></td>
                <td></td>
            </tr>
        </tfoot>
        <?php endif; ?>
    </table>

    <!-- Pie -->
    <div class="rpt-footer">
        <span>StoreControl &mdash; <?= htmlspecialchars($sucursal_nombre) ?></span>
        <span>Reporte generado el <?= date('d/m/Y \a \l\a\s H:i') ?></span>
    </div>

</body>
<script>window.onload = function(){ window.print(); }</script>
</html>
