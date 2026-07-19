<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StoreControl &mdash; Categorías</title>
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
                <h5 class="topbar-title mb-0">Categorías</h5>
                <p class="topbar-date mb-0">Organiza los productos del almacén por categoría</p>
            </div>
            <div class="topbar-right">
                <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal"
                        data-bs-target="#modalCategoria" onclick="nuevaCategoria()">
                    <i class="bi bi-plus-lg me-1"></i> Nueva categoría
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
                        <div class="stat-icon bg-primary-soft">
                            <i class="bi bi-tags-fill text-primary"></i>
                        </div>
                        <div>
                            <div class="stat-value"><?= $totales['total'] ?? 0 ?></div>
                            <div class="stat-label">Total categorías</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-success-soft">
                            <i class="bi bi-check-circle-fill text-success"></i>
                        </div>
                        <div>
                            <div class="stat-value"><?= $totales['activas'] ?? 0 ?></div>
                            <div class="stat-label">Activas</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-secondary-soft">
                            <i class="bi bi-slash-circle-fill text-secondary"></i>
                        </div>
                        <div>
                            <div class="stat-value"><?= $totales['inactivas'] ?? 0 ?></div>
                            <div class="stat-label">Inactivas</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla -->
            <div class="card border-0 shadow-sm" style="border-radius:.875rem;">
                <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between pt-3 pb-2 gap-2">
                    <h6 class="fw-bold mb-0">Listado de categorías</h6>
                    <input type="text" class="form-control form-control-sm" id="buscador"
                           placeholder="Buscar categoría…" style="width:220px;">
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="tablaCategorias">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">#</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th class="text-center">Productos</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($categorias)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="bi bi-tags fs-2 d-block mb-2 opacity-25"></i>
                                        No hay categorías registradas.
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($categorias as $i => $c): ?>
                                <?php $inactivo = !(bool)$c['activo']; ?>
                                <tr class="<?= $inactivo ? 'fila-bloqueada' : '' ?>">
                                    <td class="ps-3 text-muted small"><?= $i + 1 ?></td>
                                    <td>
                                        <div class="fw-semibold <?= $inactivo ? 'text-muted' : '' ?>">
                                            <?= htmlspecialchars($c['nombre']) ?>
                                            <?php if ($inactivo): ?>
                                            <span class="badge bg-secondary ms-1" style="font-size:.62rem;">Inactiva</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="small text-muted"><?= htmlspecialchars($c['descripcion'] ?: '—') ?></td>
                                    <td class="text-center">
                                        <span class="badge categoria-badge"><?= (int)$c['total_productos'] ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!$inactivo): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            <i class="bi bi-circle-fill me-1" style="font-size:.5rem;"></i>Activa
                                        </span>
                                        <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                            <i class="bi bi-circle me-1" style="font-size:.5rem;"></i>Inactiva
                                        </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button class="btn btn-sm btn-outline-primary btn-accion" title="Editar"
                                                    onclick="editarCategoria(<?= htmlspecialchars(json_encode($c)) ?>)">
                                                <i class="bi bi-pencil-fill"></i>
                                            </button>
                                            <a href="<?= BASE_URL ?>/categorias/toggle?id=<?= $c['id'] ?>"
                                               class="btn btn-sm btn-accion <?= $inactivo ? 'btn-outline-success' : 'btn-outline-warning' ?>"
                                               title="<?= $inactivo ? 'Activar' : 'Desactivar' ?>"
                                               onclick="return confirm('<?= $inactivo ? '¿Activar' : '¿Desactivar' ?> <?= htmlspecialchars($c['nombre']) ?>?')">
                                                <i class="bi <?= $inactivo ? 'bi-check-circle-fill' : 'bi-slash-circle-fill' ?>"></i>
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
            </div>

        </main>
    </div>
</div>

<!-- Modal Categoría -->
<div class="modal fade" id="modalCategoria" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="<?= BASE_URL ?>/categorias/guardar">
                <input type="hidden" name="id" id="cat-id" value="0">

                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-tags-fill me-2 text-primary"></i>
                        <span id="cat-titulo">Nueva categoría</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body pt-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nombre" id="cat-nombre"
                               placeholder="Ej: Bebidas, Abarrotes, Limpieza…" required>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-semibold small">Descripción</label>
                        <textarea class="form-control" name="descripcion" id="cat-descripcion" rows="2"
                                  placeholder="Descripción opcional…"></textarea>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="bi bi-save me-1"></i> <span id="cat-btn">Registrar categoría</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const _campos = ['nombre', 'descripcion'];

function nuevaCategoria() {
    document.getElementById('cat-titulo').textContent = 'Nueva categoría';
    document.getElementById('cat-btn').textContent    = 'Registrar categoría';
    document.getElementById('cat-id').value = '0';
    _campos.forEach(f => { const el = document.getElementById('cat-' + f); if (el) el.value = ''; });
}

function editarCategoria(c) {
    document.getElementById('cat-titulo').textContent = 'Editar categoría';
    document.getElementById('cat-btn').textContent    = 'Guardar cambios';
    document.getElementById('cat-id').value = c.id;
    _campos.forEach(f => { const el = document.getElementById('cat-' + f); if (el) el.value = c[f] || ''; });
    new bootstrap.Modal(document.getElementById('modalCategoria')).show();
}

document.getElementById('buscador').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#tablaCategorias tbody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
</body>
</html>
