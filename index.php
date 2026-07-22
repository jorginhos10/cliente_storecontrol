<?php

define('ROOT', __DIR__);
define('BASE_URL', '');

session_start();

// Router simple
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

switch ($uri) {
    case '':
    case 'login':
        require_once ROOT . '/app/controllers/AuthController.php';
        (new AuthController())->login();
        break;

    case 'logout':
        require_once ROOT . '/app/controllers/AuthController.php';
        (new AuthController())->logout();
        break;

    case 'registro':
        require_once ROOT . '/app/controllers/AuthController.php';
        (new AuthController())->registro();
        break;

    case 'dashboard':
        require_once ROOT . '/app/controllers/DashboardController.php';
        (new DashboardController())->index();
        break;

    case 'almacen':
        require_once ROOT . '/app/controllers/AlmacenController.php';
        (new AlmacenController())->index();
        break;

    case 'almacen/transferir':
        require_once ROOT . '/app/controllers/AlmacenController.php';
        (new AlmacenController())->transferir();
        break;

    case 'almacen/agregar':
        require_once ROOT . '/app/controllers/AlmacenController.php';
        (new AlmacenController())->agregar();
        break;

    case 'almacen/toggle':
        require_once ROOT . '/app/controllers/AlmacenController.php';
        (new AlmacenController())->toggle();
        break;

    case 'categorias':
        require_once ROOT . '/app/controllers/CategoriaController.php';
        (new CategoriaController())->index();
        break;

    case 'categorias/guardar':
        require_once ROOT . '/app/controllers/CategoriaController.php';
        (new CategoriaController())->guardar();
        break;

    case 'categorias/toggle':
        require_once ROOT . '/app/controllers/CategoriaController.php';
        (new CategoriaController())->toggle();
        break;

    case 'clientes':
        require_once ROOT . '/app/controllers/ClienteController.php';
        (new ClienteController())->index();
        break;

    case 'deudas':
        require_once ROOT . '/app/controllers/DeudaController.php';
        (new DeudaController())->index();
        break;

    case 'deudas/registrar':
        require_once ROOT . '/app/controllers/DeudaController.php';
        (new DeudaController())->registrar();
        break;

    case 'deudas/pagar':
        require_once ROOT . '/app/controllers/DeudaController.php';
        (new DeudaController())->pagar();
        break;

    case 'deudas/eliminar':
        require_once ROOT . '/app/controllers/DeudaController.php';
        (new DeudaController())->eliminar();
        break;

    case 'clientes/guardar':
        require_once ROOT . '/app/controllers/ClienteController.php';
        (new ClienteController())->guardar();
        break;

    case 'clientes/eliminar':
        require_once ROOT . '/app/controllers/ClienteController.php';
        (new ClienteController())->eliminar();
        break;

    case 'usuarios':
        require_once ROOT . '/app/controllers/UsuarioController.php';
        (new UsuarioController())->index();
        break;

    case 'usuarios/guardar':
        require_once ROOT . '/app/controllers/UsuarioController.php';
        (new UsuarioController())->guardar();
        break;

    case 'usuarios/toggle':
        require_once ROOT . '/app/controllers/UsuarioController.php';
        (new UsuarioController())->toggle();
        break;

    case 'usuarios/password':
        require_once ROOT . '/app/controllers/UsuarioController.php';
        (new UsuarioController())->cambiarPassword();
        break;

    case 'configuracion':
        require_once ROOT . '/app/controllers/ConfiguracionController.php';
        (new ConfiguracionController())->index();
        break;

    case 'configuracion/sucursales':
        require_once ROOT . '/app/controllers/ConfiguracionController.php';
        (new ConfiguracionController())->sucursales();
        break;

    case 'configuracion/veterinaria/guardar':
        require_once ROOT . '/app/controllers/ConfiguracionController.php';
        (new ConfiguracionController())->guardarVeterinaria();
        break;

    case 'configuracion/veterinaria/eliminar':
        require_once ROOT . '/app/controllers/ConfiguracionController.php';
        (new ConfiguracionController())->eliminarVeterinaria();
        break;

    case 'ingresos':
        require_once ROOT . '/app/controllers/IngresoController.php';
        (new IngresoController())->index();
        break;

    case 'ingresos/registrar':
        require_once ROOT . '/app/controllers/IngresoController.php';
        (new IngresoController())->registrar();
        break;

    case 'ingresos/eliminar':
        require_once ROOT . '/app/controllers/IngresoController.php';
        (new IngresoController())->eliminar();
        break;

    case 'ingresos/detalle':
        require_once ROOT . '/app/controllers/IngresoController.php';
        (new IngresoController())->detalle();
        break;

    case 'ingresos/editar':
        require_once ROOT . '/app/controllers/IngresoController.php';
        (new IngresoController())->editar();
        break;

    case 'perdidas':
        require_once ROOT . '/app/controllers/PerdidaController.php';
        (new PerdidaController())->index();
        break;

    case 'perdidas/registrar':
        require_once ROOT . '/app/controllers/PerdidaController.php';
        (new PerdidaController())->registrar();
        break;

    case 'perdidas/editar':
        require_once ROOT . '/app/controllers/PerdidaController.php';
        (new PerdidaController())->editar();
        break;

    case 'perdidas/detalle':
        require_once ROOT . '/app/controllers/PerdidaController.php';
        (new PerdidaController())->detalle();
        break;

    case 'perdidas/eliminar':
        require_once ROOT . '/app/controllers/PerdidaController.php';
        (new PerdidaController())->eliminar();
        break;

    case 'ventas':
        require_once ROOT . '/app/controllers/VentaController.php';
        (new VentaController())->index();
        break;

    case 'proveedores':
        require_once ROOT . '/app/controllers/ProveedorController.php';
        (new ProveedorController())->index();
        break;

    case 'proveedores/guardar':
        require_once ROOT . '/app/controllers/ProveedorController.php';
        (new ProveedorController())->guardar();
        break;

    case 'proveedores/toggle':
        require_once ROOT . '/app/controllers/ProveedorController.php';
        (new ProveedorController())->toggle();
        break;

    case 'reportes/ingresos':
        require_once ROOT . '/app/controllers/IngresoController.php';
        (new IngresoController())->reporte();
        break;

    case 'reportes/ingresos/imprimir':
        require_once ROOT . '/app/controllers/IngresoController.php';
        (new IngresoController())->imprimirReporte();
        break;

    case 'reportes/perdidas':
        require_once ROOT . '/app/controllers/PerdidaController.php';
        (new PerdidaController())->reporte();
        break;

    case 'reportes/perdidas/imprimir':
        require_once ROOT . '/app/controllers/PerdidaController.php';
        (new PerdidaController())->imprimirReporte();
        break;

    case 'reportes/ventas':
        require_once ROOT . '/app/controllers/VentaController.php';
        (new VentaController())->reporteVentas();
        break;

    case 'reportes/unidades':
        require_once ROOT . '/app/controllers/VentaController.php';
        (new VentaController())->reporteUnidades();
        break;

    case 'reportes/unidades/imprimir':
        require_once ROOT . '/app/controllers/VentaController.php';
        (new VentaController())->imprimirUnidades();
        break;

    case 'reportes/ventas/imprimir':
        require_once ROOT . '/app/controllers/VentaController.php';
        (new VentaController())->imprimirReporte();
        break;

    case 'ventas/historial':
        require_once ROOT . '/app/controllers/VentaController.php';
        (new VentaController())->historial();
        break;

    case 'ventas/registrar':
        require_once ROOT . '/app/controllers/VentaController.php';
        (new VentaController())->registrar();
        break;

    case 'ventas/detalle':
        require_once ROOT . '/app/controllers/VentaController.php';
        (new VentaController())->detalle();
        break;

    case 'ventas/anular':
        require_once ROOT . '/app/controllers/VentaController.php';
        (new VentaController())->anular();
        break;

    case 'ventas/editar':
        require_once ROOT . '/app/controllers/VentaController.php';
        (new VentaController())->editar();
        break;

    case 'ventas/factura':
        require_once ROOT . '/app/controllers/VentaController.php';
        (new VentaController())->imprimirFactura();
        break;

    case 'devoluciones':
        require_once ROOT . '/app/controllers/DevolucionController.php';
        (new DevolucionController())->index();
        break;

    case 'devoluciones/registrar':
        require_once ROOT . '/app/controllers/DevolucionController.php';
        (new DevolucionController())->registrar();
        break;

    case 'devoluciones/lineas':
        require_once ROOT . '/app/controllers/DevolucionController.php';
        (new DevolucionController())->lineas();
        break;

    case 'devoluciones/detalle':
        require_once ROOT . '/app/controllers/DevolucionController.php';
        (new DevolucionController())->detalle();
        break;

    case 'set_vet':
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
        // Doble verificación server-side: sesión fija O rol no admin
        $back = $_GET['back'] ?? (BASE_URL . '/dashboard');
        if (!empty($_SESSION['sucursal_fija']) || ($_SESSION['usuario_rol'] ?? '') !== 'admin') {
            if (strpos($back, '/') !== 0 || strpos($back, '://') !== false) {
                $back = BASE_URL . '/dashboard';
            }
            header('Location: ' . $back);
            exit;
        }
        if (isset($_GET['vet'])) {
            $vetId = (int)$_GET['vet'];
            $_SESSION['veterinaria_id'] = $vetId;
            require_once ROOT . '/app/models/UsuarioModel.php';
            (new UsuarioModel())->guardarUltimaVeterinaria((int)$_SESSION['usuario_id'], $vetId);
        }
        $back = $_GET['back'] ?? (BASE_URL . '/dashboard');
        if (strpos($back, '/') !== 0 || strpos($back, '://') !== false) {
            $back = BASE_URL . '/dashboard';
        }
        header('Location: ' . $back);
        exit;

    default:
        http_response_code(404);
        echo '<h1>404 - Página no encontrada</h1>';
        break;
}
