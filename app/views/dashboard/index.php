<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StoreControl &mdash; Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="dashboard-body">

<div class="app-layout">

    <?php require ROOT . '/app/views/layouts/sidebar.php'; ?>

    <!-- ── CONTENIDO PRINCIPAL ──────────────────────── -->
    <div class="main-wrapper">

        <!-- Topbar -->
        <header class="topbar">
            <div class="topbar-left">
                <h5 class="topbar-title mb-0">Dashboard</h5>
                <p class="topbar-date mb-0"><?= date('l, d \d\e F \d\e Y') ?></p>
            </div>
            <div class="topbar-right">
                <!-- Veterinaria -->
                <?php require ROOT . '/app/views/layouts/vet_selector.php'; ?>

                <a href="<?= BASE_URL ?>/ventas" class="btn btn-primary-custom btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> Nueva venta
                </a>
                <div class="dropdown">
                    <button class="btn btn-link topbar-avatar-btn p-0" data-bs-toggle="dropdown">
                        <div class="avatar-md">
                            <?= strtoupper(substr($usuario['nombre'], 0, 1)) ?>
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li>
                            <div class="dropdown-header">
                                <div class="fw-semibold"><?= htmlspecialchars($usuario['nombre']) ?></div>
                                <div class="text-muted small"><?= htmlspecialchars($usuario['email']) ?></div>
                                <span class="badge badge-rol mt-1"><?= htmlspecialchars(ucfirst($usuario['rol'])) ?></span>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Mi perfil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Configuración</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="<?= BASE_URL ?>/logout" onclick="return confirm('¿Cerrar sesión?')">
                                <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Contenido -->
        <main class="main-content">

            <!-- Stat cards -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-primary-soft">
                            <i class="bi bi-cart-check-fill text-primary"></i>
                        </div>
                        <div>
                            <div class="stat-value"><?= count($ventasHoy) ?></div>
                            <div class="stat-label">Ventas hoy</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-success-soft">
                            <i class="bi bi-currency-dollar text-success"></i>
                        </div>
                        <div>
                            <div class="stat-value">$<?= number_format($ventasTotales['ingresos_hoy'] ?? 0, 2) ?></div>
                            <div class="stat-label">Ingresos de hoy</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-warning-soft">
                            <i class="bi bi-people-fill text-warning"></i>
                        </div>
                        <div>
                            <div class="stat-value"><?= $clientesTotales['total'] ?? 0 ?></div>
                            <div class="stat-label">Clientes activos</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-info-soft">
                            <i class="bi bi-exclamation-triangle-fill text-info"></i>
                        </div>
                        <div>
                            <div class="stat-value"><?= $productosTotales['stock_bajo'] ?? 0 ?></div>
                            <div class="stat-label">Productos con stock bajo</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla + accesos -->
            <div class="row g-3">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 pt-3 pb-2 d-flex align-items-center justify-content-between">
                            <h6 class="card-title mb-0 fw-semibold">Ventas de hoy</h6>
                            <a href="<?= BASE_URL ?>/ventas/historial" class="small text-decoration-none">Ver todas</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3">Hora</th>
                                            <th>Cliente</th>
                                            <th>Vendedor</th>
                                            <th class="text-center">Productos</th>
                                            <th class="text-end">Total</th>
                                            <th class="text-center">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($ventasHoy)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-5">
                                                <i class="bi bi-cart fs-2 d-block mb-2 opacity-25"></i>
                                                Aún no hay ventas registradas hoy.
                                            </td>
                                        </tr>
                                        <?php else: ?>
                                        <?php foreach ($ventasHoy as $v): ?>
                                        <tr>
                                            <td class="ps-3 text-muted small"><?= date('H:i', strtotime($v['created_at'])) ?></td>
                                            <td><span class="fw-semibold"><?= htmlspecialchars(trim($v['cliente_nombre']) ?: 'Cliente general') ?></span></td>
                                            <td class="text-muted small"><?= htmlspecialchars($v['vendedor_nombre'] ?: '—') ?></td>
                                            <td class="text-center">
                                                <span class="badge categoria-badge"><?= (int)$v['total_lineas'] ?> prod.</span>
                                            </td>
                                            <td class="text-end fw-semibold">$<?= number_format($v['total'], 2) ?></td>
                                            <td class="text-center">
                                                <?php if ($v['estado'] === 'anulada'): ?>
                                                <span class="badge bg-danger-soft text-danger">Cancelada</span>
                                                <?php elseif ($v['estado'] === 'pendiente'): ?>
                                                <span class="badge bg-warning-soft text-warning">Pendiente</span>
                                                <?php else: ?>
                                                <span class="badge bg-success-soft text-success">Completada</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-0 pt-3 pb-2">
                            <h6 class="card-title mb-0 fw-semibold">Accesos rápidos</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="<?= BASE_URL ?>/ventas" class="btn btn-light text-start d-flex align-items-center gap-2">
                                    <i class="bi bi-cart-plus text-primary"></i> Nueva venta
                                </a>
                                <a href="<?= BASE_URL ?>/clientes" class="btn btn-light text-start d-flex align-items-center gap-2">
                                    <i class="bi bi-person-plus text-warning"></i> Registrar cliente
                                </a>
                                <a href="<?= BASE_URL ?>/almacen" class="btn btn-light text-start d-flex align-items-center gap-2">
                                    <i class="bi bi-box-seam text-success"></i> Agregar producto
                                </a>
                                <a href="<?= BASE_URL ?>/ingresos" class="btn btn-light text-start d-flex align-items-center gap-2">
                                    <i class="bi bi-box-arrow-in-down-right text-info"></i> Registrar ingreso
                                </a>
                                <a href="<?= BASE_URL ?>/reportes/ventas" class="btn btn-light text-start d-flex align-items-center gap-2">
                                    <i class="bi bi-bar-chart-fill text-secondary"></i> Ver reportes
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div><!-- /.main-wrapper -->

</div><!-- /.app-layout -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
