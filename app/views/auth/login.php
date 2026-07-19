<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StoreControl &mdash; Iniciar Sesión</title>
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
            <p class="brand-desc">Sistema de punto de venta integral para la gestión de tu tienda.</p>
            <ul class="feature-list">
                <li><i class="bi bi-check2-circle"></i> Registro rápido de ventas</li>
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

            <h3 class="login-title">Bienvenido de vuelta</h3>
            <p class="login-subtitle">Ingresa tus credenciales para continuar</p>

            <!-- Alerta de error -->
            <?php if (!empty($error)): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2 py-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
            <?php endif; ?>

            <!-- Formulario -->
            <form method="POST" action="<?= BASE_URL ?>/" id="loginForm" novalidate>

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Correo electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input
                            type="email"
                            class="form-control <?= !empty($error) ? 'is-invalid' : '' ?>"
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
                            placeholder="Tu contraseña"
                            autocomplete="current-password"
                            required
                        >
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword" tabindex="-1" title="Mostrar/ocultar contraseña">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label text-muted" for="remember">Recordarme</label>
                    </div>
                    <a href="#" class="link-primary text-decoration-none small">¿Olvidaste tu contraseña?</a>
                </div>

                <button type="submit" class="btn btn-primary-custom w-100" id="btnLogin">
                    <span class="btn-text"><i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión</span>
                    <span class="btn-loading d-none">
                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>Ingresando...
                    </span>
                </button>

            </form>

            <!-- Credenciales de prueba -->
            <div class="demo-section mt-4">
                <p class="text-center text-muted small mb-2">Usuarios de prueba</p>
                <div class="demo-btns">
                    <button class="demo-btn" onclick="fillDemo('admin@storecontrol.com')" title="Admin">
                        <i class="bi bi-shield-check"></i> Admin
                    </button>
                    <button class="demo-btn" onclick="fillDemo('veterinario@storecontrol.com')" title="Veterinario">
                        <i class="bi bi-clipboard2-pulse"></i> Veterinario
                    </button>
                    <button class="demo-btn" onclick="fillDemo('recepcion@storecontrol.com')" title="Recepción">
                        <i class="bi bi-person-badge"></i> Recepción
                    </button>
                </div>
                <p class="text-center text-muted mt-1" style="font-size: .75rem;">
                    Contraseña: <code>password</code>
                </p>
            </div>

            <p class="text-center text-muted mt-4 mb-0" style="font-size: .75rem;">
                &copy; <?= date('Y') ?> StoreControl. Todos los derechos reservados.
            </p>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
</body>
</html>
