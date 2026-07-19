<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StoreControl &mdash; Reporte de Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
    <style>
        .rpt-filter {
            background: #fff;
            border: 1px solid #e8eaf0;
            border-radius: 1rem;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: flex-end;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }
        .rpt-filter label { font-weight: 600; font-size: .82rem; color: #374151; margin-bottom: .3rem; }

        .top-list { list-style: none; padding: 0; margin: 0; }
        .top-list li {
            display: flex; align-items: center; gap: .75rem;
            padding: .6rem 0; border-bottom: 1px solid #f3f4f6;
        }
        .top-list li:last-child { border-bottom: none; }
        .top-rank {
            width: 26px; height: 26px; border-radius: 50%;
            background: #eef2ff; color: #4f46e5;
            font-size: .72rem; font-weight: 800;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .top-rank.gold   { background: #fef9c3; color: #854d0e; }
        .top-rank.silver { background: #f1f5f9; color: #475569; }
        .top-rank.bronze { background: #fef3c7; color: #92400e; }
        .top-bar-wrap { flex: 1; }
        .top-bar-label { font-size: .8rem; font-weight: 600; }
        .top-bar-sub   { font-size: .72rem; color: #9ca3af; }
        .top-bar-track {
            height: 6px; background: #f3f4f6; border-radius: 99px; margin-top: .3rem;
        }
        .top-bar-fill {
            height: 100%; border-radius: 99px;
            background: linear-gradient(90deg, #4f46e5, #818cf8);
            transition: width .6s ease;
        }
        .top-val { font-weight: 700; font-size: .82rem; color: #1f2937; white-space: nowrap; }
    </style>
</head>
<body class="dashboard-body">

<div class="app-layout">

    <?php require ROOT . '/app/views/layouts/sidebar.php'; ?>

    <div class="main-wrapper">

        <header class="topbar">
            <div class="topbar-left">
                <h5 class="topbar-title mb-0">Reporte de Ventas</h5>
                <p class="topbar-date mb-0">
                    <?= date('d/m/Y', strtotime($desde)) ?>
                    <?= $desde !== $hasta ? ' → ' . date('d/m/Y', strtotime($hasta)) : '' ?>
                </p>
            </div>
            <div class="topbar-right">
                <?php require ROOT . '/app/views/layouts/vet_selector.php'; ?>
                <div class="dropdown">
                    <button class="btn btn-link topbar-avatar-btn p-0" data-bs-toggle="dropdown">
                        <div class="avatar-md"><?= strtoupper(substr($usuario['nombre'], 0, 1)) ?></div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li>
                            <div class="dropdown-header">
                                <div class="fw-semibold"><?= htmlspecialchars($usuario['nombre']) ?></div>
                                <div class="text-muted small"><?= htmlspecialchars($usuario['email']) ?></div>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="<?= BASE_URL ?>/logout"
                               onclick="return confirm('¿Cerrar sesión?')">
                                <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="main-content">

            <!-- Filtro de fechas -->
            <form method="GET" action="<?= BASE_URL ?>/reportes/ventas" class="rpt-filter">
                <div>
                    <label>Desde</label>
                    <input type="date" class="form-control form-control-sm" name="desde"
                           value="<?= $desde ?>" max="<?= date('Y-m-d') ?>">
                </div>
                <div>
                    <label>Hasta</label>
                    <input type="date" class="form-control form-control-sm" name="hasta"
                           value="<?= $hasta ?>" max="<?= date('Y-m-d') ?>">
                </div>
                <button type="submit" class="btn btn-primary-custom btn-sm">
                    <i class="bi bi-search me-1"></i> Consultar
                </button>
                <a href="<?= BASE_URL ?>/reportes/ventas" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Hoy
                </a>
                <a href="<?= BASE_URL ?>/reportes/ventas/imprimir?desde=<?= $desde ?>&hasta=<?= $hasta ?>"
                   target="_blank" class="btn btn-outline-secondary btn-sm ms-auto">
                    <i class="bi bi-printer me-1"></i> Imprimir / PDF
                </a>
            </form>

            <!-- Stats -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-success-soft">
                            <i class="bi bi-bag-check-fill text-success"></i>
                        </div>
                        <div>
                            <div class="stat-value"><?= $resumen['ventas_completadas'] ?? 0 ?></div>
                            <div class="stat-label">Ventas completadas</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-primary-soft">
                            <i class="bi bi-currency-dollar text-primary"></i>
                        </div>
                        <div>
                            <div class="stat-value">$<?= number_format($resumen['total_ingresos'] ?? 0, 2) ?></div>
                            <div class="stat-label">Total ingresos</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-warning-soft">
                            <i class="bi bi-graph-up text-warning"></i>
                        </div>
                        <div>
                            <div class="stat-value">$<?= number_format($resumen['promedio_venta'] ?? 0, 2) ?></div>
                            <div class="stat-label">Promedio por venta</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-danger-soft">
                            <i class="bi bi-bag-x-fill text-danger"></i>
                        </div>
                        <div>
                            <div class="stat-value"><?= $resumen['ventas_anuladas'] ?? 0 ?></div>
                            <div class="stat-label">Canceladas</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top productos y vendedores -->
            <div class="row g-3 mb-4">

                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100" style="border-radius:.875rem;">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-box-seam-fill text-primary"></i>
                                Top productos vendidos
                            </h6>
                            <?php if (empty($top_productos)): ?>
                            <p class="text-muted small text-center py-3">Sin datos en este período.</p>
                            <?php else: ?>
                            <?php
                            $maxUnidades = max(array_column($top_productos, 'total_unidades')) ?: 1;
                            $rankColors  = ['gold', 'silver', 'bronze', '', ''];
                            ?>
                            <ul class="top-list">
                                <?php foreach ($top_productos as $idx => $p): ?>
                                <li>
                                    <div class="top-rank <?= $rankColors[$idx] ?? '' ?>"><?= $idx + 1 ?></div>
                                    <div class="top-bar-wrap">
                                        <div class="top-bar-label"><?= htmlspecialchars($p['nombre']) ?></div>
                                        <div class="top-bar-sub"><?= number_format($p['total_unidades']) ?> unidades</div>
                                        <div class="top-bar-track">
                                            <div class="top-bar-fill" style="width:<?= round($p['total_unidades'] / $maxUnidades * 100) ?>%"></div>
                                        </div>
                                    </div>
                                    <div class="top-val">$<?= number_format($p['total_ingresos'], 2) ?></div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100" style="border-radius:.875rem;">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-person-fill text-success"></i>
                                Top vendedores
                            </h6>
                            <?php if (empty($top_vendedores)): ?>
                            <p class="text-muted small text-center py-3">Sin datos en este período.</p>
                            <?php else: ?>
                            <?php $maxIngresos = max(array_column($top_vendedores, 'total_ingresos')) ?: 1; ?>
                            <ul class="top-list">
                                <?php foreach ($top_vendedores as $idx => $v): ?>
                                <li>
                                    <div class="top-rank <?= $rankColors[$idx] ?? '' ?>"><?= $idx + 1 ?></div>
                                    <div class="top-bar-wrap">
                                        <div class="top-bar-label"><?= htmlspecialchars($v['nombre']) ?></div>
                                        <div class="top-bar-sub"><?= $v['total_ventas'] ?> ventas</div>
                                        <div class="top-bar-track">
                                            <div class="top-bar-fill" style="width:<?= round($v['total_ingresos'] / $maxIngresos * 100) ?>%; background:linear-gradient(90deg,#10b981,#6ee7b7);"></div>
                                        </div>
                                    </div>
                                    <div class="top-val">$<?= number_format($v['total_ingresos'], 2) ?></div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Tabla detalle -->
            <div class="card border-0 shadow-sm" style="border-radius:.875rem;">
                <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between pt-3 pb-2">
                    <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-list-ul text-primary"></i>
                        Detalle de ventas
                        <span class="badge bg-primary-soft text-primary fw-semibold" style="font-size:.75rem;">
                            <?= count($ventas) ?> registros
                        </span>
                    </h6>
                    <?php if (!empty($resumen['total_descuentos'])): ?>
                    <span class="text-muted small">
                        Descuentos aplicados: <strong>$<?= number_format($resumen['total_descuentos'], 2) ?></strong>
                    </span>
                    <?php endif; ?>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <?php
                                require_once ROOT . '/app/models/VeterinariaModel.php';
                                $sucNombre = '';
                                foreach ($veterinarias as $_sv) {
                                    if ((int)$_sv['id'] === (int)$veterinaria_id) { $sucNombre = $_sv['nombre']; break; }
                                }
                            ?>
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Radicado</th>
                                    <th>Cliente</th>
                                    <th>Vendedor</th>
                                    <th class="text-center">Productos</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-end">Descuento</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-center">Fecha y hora</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($ventas)): ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-5">
                                        <i class="bi bi-bar-chart fs-2 d-block mb-2 opacity-25"></i>
                                        No hay ventas en el período seleccionado.
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($ventas as $i => $v): ?>
                                <?php
                                    $anulada  = $v['estado'] === 'anulada';
                                    $radicado = VeterinariaModel::serialVenta($sucNombre, $v['id'], $v['created_at']);
                                ?>
                                <tr class="<?= $anulada ? 'fila-bloqueada' : '' ?>">
                                    <td class="ps-3">
                                        <span style="font-family:monospace;font-size:.72rem;font-weight:700;
                                                     background:#f5f3ff;color:#4f46e5;border:1px solid #c7d2fe;
                                                     border-radius:.3rem;padding:.1rem .4rem;white-space:nowrap;">
                                            <?= htmlspecialchars($radicado) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="cliente-avatar" style="width:28px;height:28px;font-size:.7rem;">
                                                <?= strtoupper(substr($v['cliente_nombre'] ?: 'G', 0, 1)) ?>
                                            </div>
                                            <span class="small fw-semibold">
                                                <?= htmlspecialchars($v['cliente_nombre'] ?: 'Cliente general') ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1">
                                            <div class="cliente-avatar" style="width:24px;height:24px;font-size:.65rem;background:var(--primary-color);">
                                                <?= strtoupper(substr($v['vendedor_nombre'] ?? 'S', 0, 1)) ?>
                                            </div>
                                            <span class="small text-muted">
                                                <?= htmlspecialchars($v['vendedor_nombre'] ?? 'Sistema') ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-<?= $anulada ? 'secondary' : 'success' ?>-soft text-<?= $anulada ? 'secondary' : 'success' ?>">
                                            <?= $v['total_lineas'] ?> prod.
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($anulada): ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Cancelada</span>
                                        <?php else: ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Completada</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end text-muted small">
                                        <?= $v['descuento'] > 0 ? '-$' . number_format($v['descuento'], 2) : '—' ?>
                                    </td>
                                    <td class="text-end fw-bold <?= $anulada ? 'text-decoration-line-through text-muted' : '' ?>">
                                        $<?= number_format($v['total'], 2) ?>
                                    </td>
                                    <td class="text-center text-muted small">
                                        <?= date('d/m/Y', strtotime($v['created_at'])) ?>
                                        <div style="font-size:.72rem;"><?= date('H:i', strtotime($v['created_at'])) ?></div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button class="btn btn-sm btn-outline-primary btn-accion" title="Ver productos"
                                                    onclick="verDetalle(<?= $v['id'] ?>, '<?= htmlspecialchars(addslashes($v['cliente_nombre'] ?: 'Cliente general')) ?>')">
                                                <i class="bi bi-eye-fill"></i>
                                            </button>
                                            <a href="<?= BASE_URL ?>/ventas/factura?id=<?= $v['id'] ?>" target="_blank"
                                               class="btn btn-sm btn-outline-secondary btn-accion" title="Imprimir factura">
                                                <i class="bi bi-printer-fill"></i>
                                            </a>
                                            <?php if (!$anulada): ?>
                                            <button class="btn btn-sm btn-outline-warning btn-accion" title="Modificar venta"
                                                    onclick="editarVenta(<?= htmlspecialchars(json_encode($v)) ?>, '<?= htmlspecialchars(addslashes($radicado)) ?>')">
                                                <i class="bi bi-pencil-fill"></i>
                                            </button>
                                            <a href="<?= BASE_URL ?>/ventas/anular?id=<?= $v['id'] ?>&vet=<?= $veterinaria_id ?>&volver=reportes/ventas"
                                               class="btn btn-sm btn-outline-danger btn-accion" title="Cancelar venta"
                                               onclick="return confirm('¿Cancelar esta venta? Se restaurará el stock.')">
                                                <i class="bi bi-x-circle-fill"></i>
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <?php if (!empty($ventas)): ?>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="6" class="text-end fw-bold ps-3">Total período</td>
                                    <td class="text-end fw-bold text-success">
                                        $<?= number_format($resumen['total_ingresos'] ?? 0, 2) ?>
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<!-- Modal detalle de venta -->
<div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-receipt-cutoff text-success me-2"></i>
                    Detalle — <span id="detalleCliente"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalleBody"></div>
            <div class="modal-footer border-0">
                <button class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal modificar venta -->
<div class="modal fade" id="modalEditarVenta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="<?= BASE_URL ?>/ventas/editar" id="formEditarVenta">
                <input type="hidden" name="venta_id" id="ev-venta-id" value="0">
                <input type="hidden" name="veterinaria_id" value="<?= $veterinaria_id ?>">
                <div id="ev-lineas-inputs"></div>

                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-pencil-square text-warning me-2"></i>
                        Modificar venta — <span id="ev-radicado"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body pt-2">

                    <div class="row g-3 mb-3 pb-3 border-bottom">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Cliente</label>
                            <select class="form-select" name="cliente_id" id="ev-cliente_id">
                                <option value="0">— Cliente general —</option>
                                <?php foreach ($clientes as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre_completo']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Notas</label>
                            <input type="text" class="form-control" name="notas" id="ev-notas" placeholder="Observaciones…">
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-semibold small text-muted text-uppercase" style="letter-spacing:.06em;">Productos</span>
                        <button type="button" class="btn btn-primary-custom btn-sm" onclick="evAgregarLinea()">
                            <i class="bi bi-plus-lg me-1"></i> Agregar producto
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-2">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center" style="width:110px;">Cantidad</th>
                                    <th class="text-end" style="width:130px;">P. Unitario</th>
                                    <th class="text-end" style="width:110px;">Subtotal</th>
                                    <th style="width:40px;"></th>
                                </tr>
                            </thead>
                            <tbody id="ev-lineas-body"></tbody>
                        </table>
                    </div>
                    <div id="ev-sin-lineas" class="text-center text-muted small py-3">Sin productos.</div>

                    <div class="row g-3 justify-content-end">
                        <div class="col-md-5">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-muted">Subtotal</span>
                                <span id="ev-subtotal">$0.00</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center small mb-1">
                                <span class="text-muted">Descuento</span>
                                <input type="number" class="form-control form-control-sm text-end" style="width:120px;"
                                       name="descuento_global" id="ev-descuento" min="0" step="0.01" value="0" oninput="evRecalcular()">
                            </div>
                            <div class="d-flex justify-content-between fw-bold fs-6 border-top pt-1">
                                <span>Total</span>
                                <span id="ev-total">$0.00</span>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom" id="ev-btn-guardar">
                        <i class="bi bi-save me-1"></i> Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function verDetalle(id, clienteNombre) {
    document.getElementById('detalleCliente').textContent = clienteNombre;
    document.getElementById('detalleBody').innerHTML =
        '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>';
    new bootstrap.Modal(document.getElementById('modalDetalle')).show();

    fetch('<?= BASE_URL ?>/ventas/detalle?id=' + id)
        .then(r => r.json())
        .then(lineas => {
            if (!lineas.length) {
                document.getElementById('detalleBody').innerHTML =
                    '<p class="text-center text-muted py-3">Sin productos.</p>';
                return;
            }
            let html = '<div class="table-responsive"><table class="table table-sm align-middle mb-0">';
            html += '<thead class="table-light"><tr><th>Producto</th><th class="text-center">Cant.</th>'
                  + '<th class="text-end">P. Unit.</th><th class="text-end">Subtotal</th></tr></thead><tbody>';
            let total = 0;
            lineas.forEach(l => {
                const sub = l.cantidad * l.precio_unitario - (parseFloat(l.descuento) || 0);
                total += sub;
                html += `<tr>
                    <td>${l.producto_nombre}<br><span class="text-muted" style="font-size:.75rem;">${l.codigo||''}</span></td>
                    <td class="text-center">${l.cantidad} ${l.unidad}</td>
                    <td class="text-end">$${parseFloat(l.precio_unitario).toFixed(2)}</td>
                    <td class="text-end fw-semibold">$${sub.toFixed(2)}</td>
                </tr>`;
            });
            html += `</tbody><tfoot class="table-light">
                <tr><td colspan="3" class="text-end fw-bold">Total</td>
                <td class="text-end fw-bold text-success">$${total.toFixed(2)}</td></tr>
            </tfoot></table></div>`;
            document.getElementById('detalleBody').innerHTML = html;
        });
}

// ── Modificar venta ────────────────────────────────────
const PRODUCTOS_V = <?= json_encode($productos) ?>;
let evLineas = [];

function editarVenta(v, radicado) {
    document.getElementById('ev-venta-id').value    = v.id;
    document.getElementById('ev-radicado').textContent = radicado;
    document.getElementById('ev-cliente_id').value  = v.cliente_id || 0;
    document.getElementById('ev-notas').value       = v.notas || '';
    document.getElementById('ev-descuento').value   = parseFloat(v.descuento) || 0;
    evLineas = [];
    document.getElementById('ev-lineas-body').innerHTML = '';
    document.getElementById('ev-sin-lineas').textContent = 'Cargando…';
    document.getElementById('ev-sin-lineas').style.display = '';

    fetch('<?= BASE_URL ?>/ventas/detalle?id=' + v.id)
        .then(r => r.json())
        .then(lineas => {
            evLineas = lineas.map(l => ({
                producto_id:     parseInt(l.producto_id),
                unidad:          l.unidad,
                cantidad:        parseInt(l.cantidad),
                precio_unitario: parseFloat(l.precio_unitario)
            }));
            evRenderLineas();
            new bootstrap.Modal(document.getElementById('modalEditarVenta')).show();
        });
}

function evAgregarLinea() {
    if (!PRODUCTOS_V.length) return;
    const p = PRODUCTOS_V[0];
    evLineas.push({ producto_id: p.id, unidad: p.unidad, cantidad: 1, precio_unitario: parseFloat(p.precio_venta) });
    evRenderLineas();
}

function evQuitarLinea(idx) {
    evLineas.splice(idx, 1);
    evRenderLineas();
}

function evCambiarProducto(idx, productoId) {
    const p = PRODUCTOS_V.find(pp => pp.id == productoId);
    if (!p) return;
    evLineas[idx].producto_id     = p.id;
    evLineas[idx].unidad          = p.unidad;
    evLineas[idx].precio_unitario = parseFloat(p.precio_venta);
    evRenderLineas();
}

function evCambiarCantidad(idx, val) {
    evLineas[idx].cantidad = Math.max(1, parseInt(val) || 1);
    evRenderLineas();
}

function evCambiarPrecio(idx, val) {
    evLineas[idx].precio_unitario = Math.max(0, parseFloat(val) || 0);
    evRenderLineas();
}

function evRenderLineas() {
    const body   = document.getElementById('ev-lineas-body');
    const vacio  = document.getElementById('ev-sin-lineas');
    const inputs = document.getElementById('ev-lineas-inputs');

    if (!evLineas.length) {
        body.innerHTML = '';
        vacio.textContent = 'Sin productos. Agrega al menos uno.';
        vacio.style.display = '';
    } else {
        vacio.style.display = 'none';
        body.innerHTML = evLineas.map((l, idx) => `
            <tr>
                <td>
                    <select class="form-select form-select-sm" onchange="evCambiarProducto(${idx}, this.value)">
                        ${PRODUCTOS_V.map(p => `<option value="${p.id}" ${p.id == l.producto_id ? 'selected' : ''}>${p.nombre}</option>`).join('')}
                    </select>
                </td>
                <td class="text-center">
                    <input type="number" class="form-control form-control-sm text-center" min="1" value="${l.cantidad}"
                           onchange="evCambiarCantidad(${idx}, this.value)">
                </td>
                <td class="text-end">
                    <input type="number" class="form-control form-control-sm text-end" min="0" step="0.01" value="${l.precio_unitario}"
                           onchange="evCambiarPrecio(${idx}, this.value)">
                </td>
                <td class="text-end fw-semibold">$${(l.cantidad * l.precio_unitario).toFixed(2)}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="evQuitarLinea(${idx})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    }

    inputs.innerHTML = evLineas.map(l => `
        <input type="hidden" name="producto_id[]" value="${l.producto_id}">
        <input type="hidden" name="cantidad[]" value="${l.cantidad}">
        <input type="hidden" name="precio_unitario[]" value="${l.precio_unitario}">
        <input type="hidden" name="desc_linea[]" value="0">
    `).join('');

    evRecalcular();
}

function evRecalcular() {
    const subtotal  = evLineas.reduce((s, l) => s + l.cantidad * l.precio_unitario, 0);
    const descuento = Math.min(parseFloat(document.getElementById('ev-descuento').value) || 0, subtotal);
    const total     = Math.max(0, subtotal - descuento);
    document.getElementById('ev-subtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('ev-total').textContent    = '$' + total.toFixed(2);
    document.getElementById('ev-btn-guardar').disabled  = evLineas.length === 0;
}
</script>
</body>
</html>
