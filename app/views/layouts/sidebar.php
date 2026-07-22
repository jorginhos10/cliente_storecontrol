<?php
// $activePage debe ser definida por el controlador antes de incluir este partial
$activePage   = $activePage ?? '';
$veterinarias = $veterinarias ?? [];
$veterinaria_id = $veterinaria_id ?? 0;
?>
<aside class="sidebar" id="sidebar">

    <!-- Logo -->
    <div class="sidebar-brand">
        <div class="sidebar-brand-icon">
            <i class="bi bi-shop"></i>
        </div>
        <span class="sidebar-brand-name">StoreControl</span>
    </div>

    <!-- Navegación -->
    <nav class="sidebar-nav">
        <ul>

            <li class="nav-section-label">Principal</li>

            <li class="<?= $activePage === 'dashboard' ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/dashboard">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="<?= $activePage === 'ventas' ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/ventas">
                    <i class="bi bi-bag-fill"></i>
                    <span>Ventas</span>
                </a>
            </li>

<li class="<?= $activePage === 'clientes' ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/clientes">
                    <i class="bi bi-people-fill"></i>
                    <span>Clientes</span>
                </a>
            </li>

            <li class="<?= $activePage === 'deudas' ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/deudas">
                    <i class="bi bi-cash-coin"></i>
                    <span>Deudas</span>
                </a>
            </li>

            <li class="nav-section-label">Inventario</li>

            <li class="<?= $activePage === 'almacen' ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/almacen">
                    <i class="bi bi-archive-fill"></i>
                    <span>Almacén</span>
                </a>
            </li>

            <li class="<?= $activePage === 'categorias' ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/categorias">
                    <i class="bi bi-tags-fill"></i>
                    <span>Categorías</span>
                </a>
            </li>

            <li class="<?= $activePage === 'ingresos' ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/ingresos">
                    <i class="bi bi-box-arrow-in-down-right"></i>
                    <span>Ingresos</span>
                </a>
            </li>

            <li class="<?= $activePage === 'perdidas' ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/perdidas">
                    <i class="bi bi-box-arrow-up-right"></i>
                    <span>Pérdidas</span>
                </a>
            </li>

            <li class="<?= $activePage === 'devoluciones' ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/devoluciones">
                    <i class="bi bi-arrow-return-left"></i>
                    <span>Devoluciones</span>
                </a>
            </li>

            <li class="nav-section-label">Reportes</li>

            <li class="<?= $activePage === 'reporte-ventas' ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/reportes/ventas">
                    <i class="bi bi-bar-chart-fill"></i>
                    <span>Reporte de ventas</span>
                </a>
            </li>

            <li class="<?= $activePage === 'reporte-unidades' ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/reportes/unidades">
                    <i class="bi bi-boxes"></i>
                    <span>Ventas por unidad</span>
                </a>
            </li>

            <li class="<?= $activePage === 'reporte-ingresos' ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/reportes/ingresos">
                    <i class="bi bi-box-arrow-in-down-right"></i>
                    <span>Reporte de ingresos</span>
                </a>
            </li>

            <li class="<?= $activePage === 'reporte-perdidas' ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/reportes/perdidas">
                    <i class="bi bi-box-arrow-up-right"></i>
                    <span>Reporte de pérdidas</span>
                </a>
            </li>

            <li class="nav-section-label">Sistema</li>

            <li class="<?= $activePage === 'configuracion' ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/configuracion">
                    <i class="bi bi-gear-fill"></i>
                    <span>Configuración</span>
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>/logout" onclick="return confirm('¿Cerrar sesión?')">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Cerrar Sesión</span>
                </a>
            </li>

        </ul>
    </nav>

    <!-- Usuario -->
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="avatar-sm">
                <?= strtoupper(substr($_SESSION['usuario_nombre'] ?? 'U', 0, 1)) ?>
            </div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name"><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? '') ?></div>
                <div class="sidebar-user-rol"><?= htmlspecialchars(ucfirst($_SESSION['usuario_rol'] ?? '')) ?></div>
            </div>
        </div>
    </div>

</aside>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var sel = document.getElementById('vet-selector');
    if (!sel) return;

    sel.addEventListener('change', function () {
        var newVet  = this.value;
        var prevVet = this.dataset.current;
        var vetName = this.options[this.selectedIndex].text;
        var el      = this;

        if (newVet === prevVet) return;

        Swal.fire({
            title: '¿Cambiar de veterinaria?',
            html: '¿Está seguro que desea cambiar a <b>' + vetName + '</b>?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor:  '#6c757d',
            confirmButtonText:  'Sí, cambiar',
            cancelButtonText:   'Cancelar',
            reverseButtons: true
        }).then(function (result) {
            if (result.isConfirmed) {
                window.location.href = '<?= BASE_URL ?>/set_vet?vet=' + newVet
                    + '&back=' + encodeURIComponent(window.location.pathname);
            } else {
                el.value = prevVet;
            }
        });
    });
});
</script>
