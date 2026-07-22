<?php

require_once ROOT . '/app/models/CategoriaModel.php';

class CategoriaController {

    private CategoriaModel $model;

    public function __construct() {
        $this->requiereAutenticacion();
        $this->model = new CategoriaModel();
    }

    public function index(): void {
        $cuenta_id = (int)($_SESSION['cuenta_id'] ?? 0);
        $datos = [
            'activePage' => 'categorias',
            'categorias' => $this->model->getAll($cuenta_id),
            'totales'    => $this->model->getTotales($cuenta_id),
            'usuario'    => [
                'nombre' => $_SESSION['usuario_nombre'],
                'email'  => $_SESSION['usuario_email'],
                'rol'    => $_SESSION['usuario_rol'],
            ],
            'success' => $_SESSION['flash_success'] ?? '',
            'error'   => $_SESSION['flash_error']   ?? '',
        ];
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);
        $this->render('categorias/index', $datos);
    }

    public function guardar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('categorias');
        }

        $d = [
            'nombre'      => trim($_POST['nombre']      ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
        ];

        if (empty($d['nombre'])) {
            $_SESSION['flash_error'] = 'El nombre de la categoría es obligatorio.';
            $this->redirect('categorias');
        }

        $id        = (int)($_POST['id'] ?? 0);
        $cuenta_id = (int)($_SESSION['cuenta_id'] ?? 0);

        if ($this->model->existeNombre($d['nombre'], $cuenta_id, $id)) {
            $_SESSION['flash_error'] = 'Ya existe una categoría con ese nombre.';
            $this->redirect('categorias');
        }

        if ($id > 0) {
            $ok = $this->model->actualizar($id, $d, $cuenta_id);
            $_SESSION[$ok ? 'flash_success' : 'flash_error'] = $ok
                ? 'Categoría actualizada correctamente.'
                : 'Error al actualizar la categoría.';
        } else {
            $ok = $this->model->crear($d, $cuenta_id);
            $_SESSION[$ok ? 'flash_success' : 'flash_error'] = $ok
                ? 'Categoría registrada correctamente.'
                : 'Error al registrar la categoría.';
        }

        $this->redirect('categorias');
    }

    public function toggle(): void {
        $id        = (int)($_GET['id'] ?? 0);
        $cuenta_id = (int)($_SESSION['cuenta_id'] ?? 0);
        if ($id > 0) $this->model->toggleActivo($id, $cuenta_id);
        $this->redirect('categorias');
    }

    private function requiereAutenticacion(): void {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '/login'); exit;
        }
    }

    private function redirect(string $ruta): void {
        header('Location: ' . BASE_URL . '/' . $ruta); exit;
    }

    private function render(string $vista, array $datos = []): void {
        extract($datos);
        require ROOT . '/app/views/' . $vista . '.php';
    }
}
