<?php

require_once ROOT . '/app/models/DevolucionModel.php';
require_once ROOT . '/app/models/VeterinariaModel.php';

class DevolucionController {

    private DevolucionModel  $model;
    private VeterinariaModel $vetModel;

    public function __construct() {
        $this->requiereAutenticacion();
        $this->model    = new DevolucionModel();
        $this->vetModel = new VeterinariaModel();
    }

    public function index(): void {
        $veterinarias   = $this->vetModel->getAll((int)($_SESSION['cuenta_id'] ?? 0));
        $veterinaria_id = (int)($_SESSION['veterinaria_id'] ?? 0);
        if ($veterinaria_id === 0 && !empty($veterinarias)) {
            $veterinaria_id = (int)$veterinarias[0]['id'];
            $_SESSION['veterinaria_id'] = $veterinaria_id;
        }

        $datos = [
            'activePage'     => 'devoluciones',
            'veterinarias'   => $veterinarias,
            'veterinaria_id' => $veterinaria_id,
            'ventas'         => $this->model->getVentas($veterinaria_id),
            'historial'      => $this->model->getHistorial($veterinaria_id),
            'totales'        => $this->model->getTotales($veterinaria_id),
            'usuario'        => [
                'nombre' => $_SESSION['usuario_nombre'],
                'email'  => $_SESSION['usuario_email'],
                'rol'    => $_SESSION['usuario_rol'],
            ],
            'success' => $_SESSION['flash_success'] ?? '',
            'error'   => $_SESSION['flash_error']   ?? '',
        ];
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);
        $this->render('devoluciones/index', $datos);
    }

    public function lineas(): void {
        $venta_id = (int)($_GET['venta_id'] ?? 0);
        header('Content-Type: application/json');
        echo json_encode($this->model->getLineasDisponibles($venta_id));
        exit;
    }

    public function detalle(): void {
        $id = (int)($_GET['id'] ?? 0);
        header('Content-Type: application/json');
        echo json_encode($this->model->getDetalleDevolucion($id));
        exit;
    }

    public function registrar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('devoluciones');
        }

        $venta_id       = (int)($_POST['venta_id'] ?? 0);
        $veterinaria_id = (int)($_POST['veterinaria_id'] ?? 0);
        $motivo         = trim($_POST['motivo'] ?? '');

        $ids        = $_POST['producto_id']      ?? [];
        $cantidades = $_POST['cantidad']         ?? [];
        $precios    = $_POST['precio_unitario']  ?? [];

        $lineas = [];
        foreach ($ids as $i => $pid) {
            $cant = (int)($cantidades[$i] ?? 0);
            if ((int)$pid > 0 && $cant > 0) {
                $lineas[] = [
                    'producto_id'     => (int)$pid,
                    'cantidad'        => $cant,
                    'precio_unitario' => (float)($precios[$i] ?? 0),
                ];
            }
        }

        if ($venta_id <= 0 || $veterinaria_id <= 0 || !$this->vetModel->findById($veterinaria_id, (int)($_SESSION['cuenta_id'] ?? 0))) {
            $_SESSION['flash_error'] = 'Datos inválidos para registrar la devolución.';
            $this->redirect('devoluciones');
        }

        $resultado = $this->model->crear(
            $venta_id, $veterinaria_id, (int)($_SESSION['usuario_id'] ?? 0), $motivo, $lineas
        );

        $_SESSION[$resultado['ok'] ? 'flash_success' : 'flash_error'] = $resultado['ok']
            ? 'Devolución registrada correctamente. Stock actualizado.'
            : ($resultado['error'] ?: 'Error al registrar la devolución.');

        $this->redirect('devoluciones');
    }

    private function requiereAutenticacion(): void {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '/');
            exit;
        }
    }

    private function redirect(string $ruta): void {
        header('Location: ' . BASE_URL . '/' . $ruta);
        exit;
    }

    private function render(string $vista, array $datos = []): void {
        extract($datos);
        require ROOT . '/app/views/' . $vista . '.php';
    }
}
