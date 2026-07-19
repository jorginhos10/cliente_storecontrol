<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StoreControl &mdash; Devoluciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="dashboard-body">
<div class="app-layout">

    <?php require ROOT . '/app/views/layouts/sidebar.php'; ?>

    <div class="main-wrapper">

        <header class="topbar">
            <div class="topbar-left">
                <h5 class="topbar-title mb-0">Devoluciones</h5>
                <p class="topbar-date mb-0">Devuelve productos o ventas completas al inventario</p>
            </div>
            <div class="topbar-right">
                <?php require ROOT . '/app/views/layouts/vet_selector.php'; ?>
                <div class="dropdown">
                    <button class="btn btn-link topbar-avatar-btn p-0" data-bs-toggle="dropdown">
                        <div class="avatar-md"><?= strtoupper(substr($usuario['nombre'], 0, 1)) ?></div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li><div class="dropdown-header">
                            <div class="fw-semibold"><?= htmlspecialchars($usuario['nombre']) ?></div>
                            <div class="text-muted small"><?= htmlspecialchars($usuario['email']) ?></div>
                        </div></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/logout"
                               onclick="return confirm('¿Cerrar sesión?')">
                            <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                        </a></li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="main-content">

            <?php if (!empty($success)): ?>
            <div class="alert alert-success d-flex align-items-center gap-2 py-2 mb-3">
                <i class="bi bi-check-circle-fill flex-shrink-0"></i>
                <span><?= htmlspecialchars($success) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2 py-2 mb-3">
                <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
            <?php endif; ?>

            <!-- Stats -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-info-soft"><i class="bi bi-arrow-return-left text-info"></i></div>
                        <div>
                            <div class="stat-value"><?= $totales['total_devoluciones'] ?? 0 ?></div>
                            <div class="stat-label">Devoluciones registradas</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-warning-soft"><i class="bi bi-boxes text-warning"></i></div>
                        <div>
                            <div class="stat-value"><?= number_format($totales['total_unidades'] ?? 0) ?></div>
                            <div class="stat-label">Unidades devueltas</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-secondary-soft"><i class="bi bi-currency-dollar text-secondary"></i></div>
                        <div>
                            <div class="stat-value">$<?= number_format($totales['valor_total'] ?? 0, 2) ?></div>
                            <div class="stat-label">Valor devuelto</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-primary-soft"><i class="bi bi-calendar-month text-primary"></i></div>
                        <div>
                            <div class="stat-value">$<?= number_format($totales['valor_mes'] ?? 0, 2) ?></div>
                            <div class="stat-label">Este mes</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ventas disponibles para devolución -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between pt-3 pb-2 gap-2 flex-wrap">
                    <h6 class="card-title mb-0 fw-semibold">Ventas</h6>
                    <input type="text" class="form-control form-control-sm" id="buscadorVentas"
                           placeholder="Buscar por radicado, cliente…" style="width:220px;">
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="tablaVentasDev">
                            <?php
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
                                    <th class="text-end">Total</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Fecha</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($ventas)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
                                        <i class="bi bi-bag fs-2 d-block mb-2 opacity-25"></i>
                                        No hay ventas registradas para esta sucursal.
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($ventas as $v): ?>
                                <?php
                                    $radicado    = VeterinariaModel::serialVenta($sucNombre, $v['id'], $v['created_at']);
                                    $totalVend   = (int)($v['total_unidades'] ?? 0);
                                    $totalDevuelto = (int)($v['total_devuelto'] ?? 0);
                                    $sinDisponible = $totalVend > 0 && $totalDevuelto >= $totalVend;
                                ?>
                                <tr>
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
                                            <span class="small fw-semibold"><?= htmlspecialchars($v['cliente_nombre'] ?: 'Cliente general') ?></span>
                                        </div>
                                    </td>
                                    <td class="small text-muted"><?= htmlspecialchars($v['vendedor_nombre'] ?: 'Sistema') ?></td>
                                    <td class="text-center">
                                        <span class="badge categoria-badge"><?= $v['total_lineas'] ?> prod.</span>
                                    </td>
                                    <td class="text-end fw-bold">$<?= number_format($v['total'], 2) ?></td>
                                    <td class="text-center">
                                        <?php if ($sinDisponible): ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Devuelta</span>
                                        <?php elseif ($totalDevuelto > 0): ?>
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Devolución parcial</span>
                                        <?php else: ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Completa</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center text-muted small">
                                        <?= date('d/m/Y', strtotime($v['created_at'])) ?>
                                        <div style="font-size:.72rem;"><?= date('H:i', strtotime($v['created_at'])) ?></div>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-info btn-accion" title="Devolver producto(s)"
                                                onclick="abrirDevolucion(<?= $v['id'] ?>, '<?= htmlspecialchars(addslashes($radicado)) ?>', '<?= htmlspecialchars(addslashes($v['cliente_nombre'] ?: 'Cliente general')) ?>')"
                                                <?= $sinDisponible ? 'disabled' : '' ?>>
                                            <i class="bi bi-arrow-return-left"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Historial de devoluciones -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between pt-3 pb-2 gap-2 flex-wrap">
                    <h6 class="card-title mb-0 fw-semibold">Historial de devoluciones</h6>
                    <input type="text" class="form-control form-control-sm" id="buscadorHist"
                           placeholder="Buscar cliente, motivo…" style="width:220px;">
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="tablaHistDev">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Venta</th>
                                    <th>Cliente</th>
                                    <th class="text-center">Tipo</th>
                                    <th class="text-center">Productos</th>
                                    <th class="text-center">Unidades</th>
                                    <th class="text-end">Valor</th>
                                    <th>Motivo</th>
                                    <th class="text-center">Fecha</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($historial)): ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-5">
                                        <i class="bi bi-inbox fs-2 d-block mb-2 opacity-25"></i>
                                        Aún no se han registrado devoluciones.
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($historial as $h): ?>
                                <?php $radVenta = VeterinariaModel::serialVenta($sucNombre, $h['venta_id'], $h['venta_fecha']); ?>
                                <tr>
                                    <td class="ps-3">
                                        <span style="font-family:monospace;font-size:.72rem;font-weight:700;
                                                     background:#f5f3ff;color:#4f46e5;border:1px solid #c7d2fe;
                                                     border-radius:.3rem;padding:.1rem .4rem;white-space:nowrap;">
                                            <?= htmlspecialchars($radVenta) ?>
                                        </span>
                                    </td>
                                    <td class="small"><?= htmlspecialchars($h['cliente_nombre'] ?: 'Cliente general') ?></td>
                                    <td class="text-center">
                                        <?php if ($h['tipo'] === 'total'): ?>
                                        <span class="badge bg-danger-soft text-danger">Venta completa</span>
                                        <?php else: ?>
                                        <span class="badge bg-info-soft text-info">Parcial</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center"><?= (int)$h['total_lineas'] ?></td>
                                    <td class="text-center fw-bold text-info"><?= number_format($h['total_unidades'] ?? 0) ?> u.</td>
                                    <td class="text-end fw-bold">$<?= number_format($h['total'], 2) ?></td>
                                    <td class="text-muted small" style="max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                        <?= htmlspecialchars($h['motivo'] ?: '—') ?>
                                    </td>
                                    <td class="text-center text-muted small">
                                        <?= date('d/m/Y', strtotime($h['created_at'])) ?>
                                        <div style="font-size:.72rem;"><?= date('H:i', strtotime($h['created_at'])) ?></div>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary btn-accion" title="Ver detalle"
                                                onclick="verDetalleDevolucion(<?= $h['id'] ?>, '<?= htmlspecialchars(addslashes($radVenta)) ?>')">
                                            <i class="bi bi-eye-fill"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<!-- Modal registrar devolución -->
<div class="modal fade" id="modalDevolucion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="<?= BASE_URL ?>/devoluciones/registrar" id="formDevolucion">
                <input type="hidden" name="venta_id" id="dv-venta-id" value="0">
                <input type="hidden" name="veterinaria_id" value="<?= $veterinaria_id ?>">
                <div id="dv-lineas-inputs"></div>

                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-arrow-return-left text-info me-2"></i>
                        Devolver — <span id="dv-radicado"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body pt-2">

                    <div class="alert alert-light border py-2 small mb-3">
                        Cliente: <strong id="dv-cliente"></strong>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-semibold small text-muted text-uppercase" style="letter-spacing:.06em;">
                            Productos de la venta
                        </span>
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="dvDevolverTodo()">
                            <i class="bi bi-arrow-return-left me-1"></i> Devolver toda la venta
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-2">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Vendido</th>
                                    <th class="text-center">Ya devuelto</th>
                                    <th class="text-center">Disponible</th>
                                    <th class="text-center" style="width:110px;">A devolver</th>
                                </tr>
                            </thead>
                            <tbody id="dv-lineas-body"></tbody>
                        </table>
                    </div>
                    <div id="dv-cargando" class="text-center text-muted small py-3">Cargando productos…</div>

                    <div class="mb-1">
                        <label class="form-label fw-semibold small">Motivo de la devolución</label>
                        <input type="text" class="form-control" name="motivo" id="dv-motivo"
                               placeholder="Ej: Producto defectuoso, cambio de talla, cliente insatisfecho…">
                    </div>

                    <div class="text-end fw-bold fs-6 mt-2">
                        Total a devolver: <span id="dv-total" class="text-info">$0.00</span>
                    </div>

                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info text-white" id="dv-btn-guardar" disabled>
                        <i class="bi bi-save me-1"></i> Registrar devolución
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal detalle de devolución -->
<div class="modal fade" id="modalDetalleDev" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-receipt-cutoff text-info me-2"></i>
                    Detalle de devolución — <span id="ddCliente"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="ddBody"></div>
            <div class="modal-footer border-0">
                <button class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
let dvLineas = [];

function abrirDevolucion(ventaId, radicado, cliente) {
    document.getElementById('dv-venta-id').value = ventaId;
    document.getElementById('dv-radicado').textContent = radicado;
    document.getElementById('dv-cliente').textContent = cliente;
    document.getElementById('dv-motivo').value = '';
    dvLineas = [];
    document.getElementById('dv-lineas-body').innerHTML = '';
    document.getElementById('dv-cargando').style.display = '';
    document.getElementById('dv-cargando').textContent = 'Cargando productos…';
    document.getElementById('dv-btn-guardar').disabled = true;

    fetch('<?= BASE_URL ?>/devoluciones/lineas?venta_id=' + ventaId)
        .then(r => r.json())
        .then(lineas => {
            dvLineas = lineas.map(l => ({
                producto_id:       parseInt(l.producto_id),
                nombre:            l.producto_nombre,
                unidad:            l.unidad,
                cantidad_vendida:  parseInt(l.cantidad),
                cantidad_devuelta: parseInt(l.cantidad_devuelta),
                precio_unitario:   parseFloat(l.precio_unitario),
                a_devolver:        0
            }));
            dvRenderLineas();
            new bootstrap.Modal(document.getElementById('modalDevolucion')).show();
        });
}

function dvRenderLineas() {
    const body     = document.getElementById('dv-lineas-body');
    const cargando = document.getElementById('dv-cargando');
    const inputs   = document.getElementById('dv-lineas-inputs');

    const conStock = dvLineas.filter(l => (l.cantidad_vendida - l.cantidad_devuelta) > 0);
    cargando.style.display = conStock.length ? 'none' : '';
    if (!conStock.length) cargando.textContent = 'No hay productos disponibles para devolver en esta venta.';

    body.innerHTML = dvLineas.map((l, idx) => {
        const disponible = l.cantidad_vendida - l.cantidad_devuelta;
        if (disponible <= 0) return '';
        return `
            <tr>
                <td>${l.nombre}</td>
                <td class="text-center">${l.cantidad_vendida} ${l.unidad}</td>
                <td class="text-center text-muted">${l.cantidad_devuelta}</td>
                <td class="text-center fw-semibold">${disponible}</td>
                <td class="text-center">
                    <input type="number" class="form-control form-control-sm text-center" min="0" max="${disponible}"
                           value="${l.a_devolver}" onchange="dvCambiarCantidad(${idx}, this.value, ${disponible})">
                </td>
            </tr>`;
    }).join('');

    inputs.innerHTML = dvLineas
        .filter(l => l.a_devolver > 0)
        .map(l => `
            <input type="hidden" name="producto_id[]" value="${l.producto_id}">
            <input type="hidden" name="cantidad[]" value="${l.a_devolver}">
            <input type="hidden" name="precio_unitario[]" value="${l.precio_unitario}">
        `).join('');

    const total = dvLineas.reduce((s, l) => s + l.a_devolver * l.precio_unitario, 0);
    document.getElementById('dv-total').textContent = '$' + total.toFixed(2);
    document.getElementById('dv-btn-guardar').disabled = !dvLineas.some(l => l.a_devolver > 0);
}

function dvCambiarCantidad(idx, val, max) {
    let cant = parseInt(val) || 0;
    if (cant < 0) cant = 0;
    if (cant > max) cant = max;
    dvLineas[idx].a_devolver = cant;
    dvRenderLineas();
}

function dvDevolverTodo() {
    dvLineas = dvLineas.map(l => ({ ...l, a_devolver: l.cantidad_vendida - l.cantidad_devuelta }));
    dvRenderLineas();
}

function verDetalleDevolucion(id, radicado) {
    document.getElementById('ddCliente').textContent = radicado;
    document.getElementById('ddBody').innerHTML =
        '<div class="text-center py-4"><div class="spinner-border text-info" role="status"></div></div>';
    new bootstrap.Modal(document.getElementById('modalDetalleDev')).show();

    fetch('<?= BASE_URL ?>/devoluciones/detalle?id=' + id)
        .then(r => r.json())
        .then(lineas => {
            if (!lineas.length) {
                document.getElementById('ddBody').innerHTML = '<p class="text-center text-muted py-3">Sin productos.</p>';
                return;
            }
            let total = 0;
            let html = '<div class="table-responsive"><table class="table table-sm align-middle mb-0">';
            html += '<thead class="table-light"><tr><th>Producto</th><th class="text-center">Cant.</th>'
                  + '<th class="text-end">P. Unit.</th><th class="text-end">Subtotal</th></tr></thead><tbody>';
            lineas.forEach(l => {
                const sub = l.cantidad * l.precio_unitario;
                total += sub;
                html += `<tr>
                    <td>${l.producto_nombre}<br><span class="text-muted" style="font-size:.75rem;">${l.codigo || ''}</span></td>
                    <td class="text-center">${l.cantidad} ${l.unidad}</td>
                    <td class="text-end">$${parseFloat(l.precio_unitario).toFixed(2)}</td>
                    <td class="text-end fw-semibold">$${sub.toFixed(2)}</td>
                </tr>`;
            });
            html += `</tbody><tfoot class="table-light">
                <tr><td colspan="3" class="text-end fw-bold">Total</td>
                <td class="text-end fw-bold text-info">$${total.toFixed(2)}</td></tr>
            </tfoot></table></div>`;
            document.getElementById('ddBody').innerHTML = html;
        });
}

function filtrarTabla(inputId, tablaId) {
    document.getElementById(inputId).addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#' + tablaId + ' tbody tr').forEach(tr => {
            tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
}
filtrarTabla('buscadorVentas', 'tablaVentasDev');
filtrarTabla('buscadorHist', 'tablaHistDev');
</script>
</body>
</html>
