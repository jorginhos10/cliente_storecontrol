<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StoreControl &mdash; Deudas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="dashboard-body">
<div class="app-layout">

    <?php require ROOT . '/app/views/layouts/sidebar.php'; ?>

    <div class="main-wrapper">

        <!-- Topbar -->
        <header class="topbar">
            <div class="topbar-left">
                <h5 class="topbar-title mb-0">Deudas</h5>
                <p class="topbar-date mb-0">Cuentas por cobrar a clientes</p>
            </div>
            <div class="topbar-right">
                <!-- Veterinaria -->
                <?php require ROOT . '/app/views/layouts/vet_selector.php'; ?>

                <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#modalDeuda">
                    <i class="bi bi-cash-coin me-1"></i> Registrar deuda
                </button>

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

            <!-- Flash -->
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
                        <div class="stat-icon bg-secondary-soft"><i class="bi bi-journal-text text-secondary"></i></div>
                        <div>
                            <div class="stat-value"><?= number_format($totales['total_registros'] ?? 0) ?></div>
                            <div class="stat-label">Registros totales</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-warning-soft"><i class="bi bi-hourglass-split text-warning"></i></div>
                        <div>
                            <div class="stat-value"><?= number_format($totales['total_pendientes'] ?? 0) ?></div>
                            <div class="stat-label">Deudas pendientes</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-danger-soft"><i class="bi bi-cash-coin text-danger"></i></div>
                        <div>
                            <div class="stat-value">$<?= number_format($totales['monto_pendiente'] ?? 0, 2) ?></div>
                            <div class="stat-label">Monto pendiente</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-success-soft"><i class="bi bi-check-circle-fill text-success"></i></div>
                        <div>
                            <div class="stat-value">$<?= number_format($totales['monto_pagado_mes'] ?? 0, 2) ?></div>
                            <div class="stat-label">Cobrado este mes</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between pt-3 pb-2 gap-2 flex-wrap">
                    <h6 class="card-title mb-0 fw-semibold">Listado de deudas</h6>
                    <input type="text" class="form-control form-control-sm" id="buscador"
                        placeholder="Buscar cliente, notas…" style="width:220px;">
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="tablaDeudas">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">#</th>
                                    <th>Cliente</th>
                                    <th class="text-end">Monto</th>
                                    <th class="text-center">Estado</th>
                                    <th>Notas</th>
                                    <th class="text-center">Fecha</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($deudas)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <i class="bi bi-cash-coin fs-2 d-block mb-2 opacity-25"></i>
                                        No hay deudas registradas.
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($deudas as $i => $d): ?>
                                <tr>
                                    <td class="ps-3 text-muted small"><?= $i + 1 ?></td>
                                    <td>
                                        <div class="fw-semibold"><?= htmlspecialchars($d['cliente_nombre']) ?></div>
                                        <?php if ($d['cliente_telefono']): ?>
                                        <div class="text-muted small"><?= htmlspecialchars($d['cliente_telefono']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end fw-bold">$<?= number_format($d['monto'], 2) ?></td>
                                    <td class="text-center">
                                        <?php if ($d['estado'] === 'pagada'): ?>
                                        <span class="badge bg-success-soft text-success">
                                            <i class="bi bi-check-circle-fill me-1"></i>Pagada
                                        </span>
                                        <?php else: ?>
                                        <span class="badge bg-warning-soft text-warning">
                                            <i class="bi bi-hourglass-split me-1"></i>Pendiente
                                        </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-muted small" style="max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                        <?= htmlspecialchars($d['notas'] ?: '—') ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="text-muted small"><?= date('d/m/Y', strtotime($d['created_at'])) ?></div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <?php if ($d['estado'] === 'pendiente'): ?>
                                            <a href="<?= BASE_URL ?>/deudas/pagar?id=<?= $d['id'] ?>&vet=<?= $veterinaria_id ?>"
                                               class="btn btn-sm btn-outline-success btn-accion" title="Marcar como pagada"
                                               onclick="return confirm('¿Marcar esta deuda como pagada?')">
                                                <i class="bi bi-check-lg"></i>
                                            </a>
                                            <?php endif; ?>
                                            <a href="<?= BASE_URL ?>/deudas/eliminar?id=<?= $d['id'] ?>&vet=<?= $veterinaria_id ?>"
                                               class="btn btn-sm btn-outline-danger btn-accion" title="Eliminar"
                                               onclick="return confirm('¿Eliminar esta deuda?')">
                                                <i class="bi bi-trash-fill"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php if (!empty($deudas)): ?>
                <div class="card-footer bg-white border-0 text-muted small d-flex justify-content-between">
                    <span>Total de registros: <?= count($deudas) ?></span>
                    <span class="fw-semibold text-body">Pendiente: $<?= number_format($totales['monto_pendiente'] ?? 0, 2) ?></span>
                </div>
                <?php endif; ?>
            </div>

        </main>
    </div>
</div>

<!-- ── MODAL REGISTRAR DEUDA ─────────────────────────── -->
<div class="modal fade" id="modalDeuda" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="<?= BASE_URL ?>/deudas/registrar" id="formDeuda">
                <input type="hidden" name="veterinaria_id" value="<?= $veterinaria_id ?>">

                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-cash-coin text-primary me-2"></i>
                        Registrar deuda
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body pt-3">
                    <div class="row g-3">

                        <div class="col-12">
                            <label class="form-label fw-semibold">Cliente <span class="text-danger">*</span></label>
                            <select class="form-select" name="cliente_id" required>
                                <option value="">Selecciona un cliente…</option>
                                <?php foreach ($clientes as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre'] . ' ' . $c['apellido']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Monto <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" name="monto" step="0.01" min="0.01" placeholder="0.00" required>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Notas</label>
                            <textarea class="form-control" name="notas" rows="2" placeholder="Observaciones adicionales…"></textarea>
                        </div>

                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="bi bi-save me-1"></i> Registrar deuda
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
<script>
document.getElementById('buscador').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#tablaDeudas tbody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
</body>
</html>
