<?php

require_once ROOT . '/app/models/DeudaModel.php';
require_once ROOT . '/app/models/ClienteModel.php';
require_once ROOT . '/app/models/VeterinariaModel.php';

class DeudaController {

    private DeudaModel      $model;
    private ClienteModel    $clienteModel;
    private VeterinariaModel $vetModel;

    public function __construct() {
        $this->requiereAutenticacion();
        $this->model        = new DeudaModel();
        $this->clienteModel = new ClienteModel();
        $this->vetModel     = new VeterinariaModel();
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
            'activePage'     => 'deudas',
            'veterinarias'   => $veterinarias,
            'veterinaria_id' => $veterinaria_id,
            'deudas'         => $this->model->getAll($veterinaria_id),
            'totales'        => $this->model->getTotales($veterinaria_id),
            'clientes'       => $this->clienteModel->getAll($cuenta_id),
            'usuario'        => [
                'nombre' => $_SESSION['usuario_nombre'],
                'email'  => $_SESSION['usuario_email'],
                'rol'    => $_SESSION['usuario_rol'],
            ],
            'success' => $_SESSION['flash_success'] ?? '',
            'error'   => $_SESSION['flash_error']   ?? '',
        ];
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        $this->render('deudas/index', $datos);
    }

    public function registrar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('deudas');
        }

        $cuenta_id      = (int)($_SESSION['cuenta_id'] ?? 0);
        $veterinaria_id = (int)($_POST['veterinaria_id'] ?? 0);
        $cliente_id     = (int)($_POST['cliente_id'] ?? 0);
        $monto          = (float)($_POST['monto'] ?? 0);
        $notas          = trim($_POST['notas'] ?? '');
        $vetQuery       = $veterinaria_id > 0 ? '?vet=' . $veterinaria_id : '';

        if (!$this->vetModel->findById($veterinaria_id, $cuenta_id)) {
            $_SESSION['flash_error'] = 'Selecciona una sucursal válida.';
            $this->redirect('deudas' . $vetQuery);
        }

        if (!$this->clienteModel->findById($cliente_id, $cuenta_id)) {
            $_SESSION['flash_error'] = 'Selecciona un cliente válido.';
            $this->redirect('deudas' . $vetQuery);
        }

        if ($monto <= 0) {
            $_SESSION['flash_error'] = 'El monto debe ser mayor a 0.';
            $this->redirect('deudas' . $vetQuery);
        }

        $ok = $this->model->crear([
            'veterinaria_id' => $veterinaria_id,
            'cliente_id'     => $cliente_id,
            'monto'          => $monto,
            'notas'          => $notas,
            'usuario_id'     => (int)($_SESSION['usuario_id'] ?? 0),
        ]);

        $_SESSION[$ok ? 'flash_success' : 'flash_error'] = $ok
            ? 'Deuda registrada correctamente.'
            : 'Error al registrar la deuda.';

        $this->redirect('deudas' . $vetQuery);
    }

    public function pagar(): void {
        $id  = (int)($_GET['id']  ?? 0);
        $vet = (int)($_GET['vet'] ?? 0);

        if ($id > 0) {
            $ok = $this->model->marcarPagada($id, $vet);
            $_SESSION[$ok ? 'flash_success' : 'flash_error'] = $ok
                ? 'Deuda marcada como pagada.'
                : 'No se pudo actualizar la deuda.';
        }

        $this->redirect('deudas' . ($vet > 0 ? '?vet=' . $vet : ''));
    }

    public function eliminar(): void {
        $id  = (int)($_GET['id']  ?? 0);
        $vet = (int)($_GET['vet'] ?? 0);

        if ($id > 0) {
            $this->model->eliminar($id, $vet);
            $_SESSION['flash_success'] = 'Deuda eliminada.';
        }

        $this->redirect('deudas' . ($vet > 0 ? '?vet=' . $vet : ''));
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
