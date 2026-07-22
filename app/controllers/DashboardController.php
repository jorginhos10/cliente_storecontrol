<?php

require_once ROOT . '/app/models/VeterinariaModel.php';
require_once ROOT . '/app/models/VentaModel.php';
require_once ROOT . '/app/models/ClienteModel.php';
require_once ROOT . '/app/models/ProductoModel.php';

class DashboardController {

    private VeterinariaModel $vetModel;
    private VentaModel       $ventaModel;
    private ClienteModel     $clienteModel;
    private ProductoModel    $productoModel;

    public function __construct() {
        $this->requiereAutenticacion();
        $this->vetModel      = new VeterinariaModel();
        $this->ventaModel    = new VentaModel();
        $this->clienteModel  = new ClienteModel();
        $this->productoModel = new ProductoModel();
    }

    public function index(): void {
        $cuenta_id      = (int)($_SESSION['cuenta_id'] ?? 0);
        $veterinarias   = $this->vetModel->getAll($cuenta_id);
        $veterinaria_id = (int)($_SESSION['veterinaria_id'] ?? 0);
        if ($veterinaria_id === 0 && !empty($veterinarias)) {
            $veterinaria_id = (int)$veterinarias[0]['id'];
            $_SESSION['veterinaria_id'] = $veterinaria_id;
        }

        $datos = [
            'activePage'      => 'dashboard',
            'veterinarias'    => $veterinarias,
            'veterinaria_id'  => $veterinaria_id,
            'ventasTotales'   => $this->ventaModel->getTotales($veterinaria_id),
            'ventasHoy'       => $this->ventaModel->getVentasHoy($veterinaria_id, 50),
            'clientesTotales' => $this->clienteModel->getTotales($cuenta_id),
            'productosTotales' => $this->productoModel->getTotales($cuenta_id, $veterinaria_id),
            'usuario' => [
                'nombre' => $_SESSION['usuario_nombre'],
                'email'  => $_SESSION['usuario_email'],
                'rol'    => $_SESSION['usuario_rol'],
            ],
        ];
        $this->render('dashboard/index', $datos);
    }

    private function requiereAutenticacion(): void {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    private function render(string $vista, array $datos = []): void {
        extract($datos);
        require ROOT . '/app/views/' . $vista . '.php';
    }
}
