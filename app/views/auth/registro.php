<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StoreControl &mdash; Crear cuenta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="login-body">

<div class="login-wrapper">

    <!-- Panel izquierdo decorativo -->
    <div class="login-panel-left d-none d-lg-flex">
        <div class="panel-content">
            <div class="brand-logo">
                <i class="bi bi-shop"></i>
            </div>
            <h1 class="brand-name">StoreControl</h1>
            <p class="brand-desc">Crea tu cuenta y arranca tu propio negocio en minutos.</p>
            <ul class="feature-list">
                <li><i class="bi bi-check2-circle"></i> Tu cuenta, tus comercios y tus datos, independientes</li>
                <li><i class="bi bi-check2-circle"></i> Control de inventario y almacén</li>
                <li><i class="bi bi-check2-circle"></i> Gestión de clientes y proveedores</li>
                <li><i class="bi bi-check2-circle"></i> Facturación y reportes</li>
            </ul>
        </div>
        <div class="paw-decorations" aria-hidden="true">
            <i class="bi bi-cart-fill paw paw-1"></i>
            <i class="bi bi-cart-fill paw paw-2"></i>
            <i class="bi bi-cart-fill paw paw-3"></i>
            <i class="bi bi-cart-fill paw paw-4"></i>
        </div>
    </div>

    <!-- Panel derecho: formulario -->
    <div class="login-panel-right">
        <div class="login-card">

            <!-- Logo mobile -->
            <div class="text-center mb-4 d-lg-none">
                <div class="brand-logo-sm">
                    <i class="bi bi-shop"></i>
                </div>
                <h2 class="mt-2 fw-bold" style="color: var(--primary);">StoreControl</h2>
            </div>

            <h3 class="login-title">Crea tu cuenta</h3>
            <p class="login-subtitle">Registra tu negocio para empezar a usar StoreControl</p>

            <!-- Alerta de error -->
            <?php if (!empty($error)): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2 py-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
            <?php endif; ?>

            <!-- Formulario -->
            <form method="POST" action="<?= BASE_URL ?>/registro" id="registroForm" novalidate>

                <div class="mb-3">
                    <label for="negocio" class="form-label fw-semibold">Nombre del negocio</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-shop"></i></span>
                        <input
                            type="text"
                            class="form-control"
                            id="negocio"
                            name="negocio"
                            placeholder="Mi Negocio"
                            value="<?= htmlspecialchars($negocio) ?>"
                            required
                        >
                    </div>
                </div>

                <div class="mb-3">
                    <label for="nombre" class="form-label fw-semibold">Tu nombre completo</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input
                            type="text"
                            class="form-control"
                            id="nombre"
                            name="nombre"
                            placeholder="Nombre y apellido"
                            value="<?= htmlspecialchars($nombre) ?>"
                            required
                        >
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Correo electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input
                            type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            placeholder="correo@ejemplo.com"
                            value="<?= htmlspecialchars($email) ?>"
                            autocomplete="email"
                            required
                        >
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input
                            type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            placeholder="Mínimo 6 caracteres"
                            autocomplete="new-password"
                            minlength="6"
                            required
                        >
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password2" class="form-label fw-semibold">Confirmar contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input
                            type="password"
                            class="form-control"
                            id="password2"
                            name="password2"
                            placeholder="Repite tu contraseña"
                            autocomplete="new-password"
                            minlength="6"
                            required
                        >
                    </div>
                </div>

                <button type="submit" class="btn btn-primary-custom w-100" id="btnRegistro">
                    <i class="bi bi-person-plus me-2"></i>Crear cuenta
                </button>

            </form>

            <p class="text-center text-muted mt-3 mb-0">
                ¿Ya tienes cuenta?
                <a href="<?= BASE_URL ?>/login" class="link-primary text-decoration-none fw-semibold">Inicia sesión</a>
            </p>

            <p class="text-center text-muted mt-4 mb-0" style="font-size: .75rem;">
                &copy; <?= date('Y') ?> StoreControl. Todos los derechos reservados.
            </p>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
