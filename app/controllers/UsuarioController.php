<?php

require_once ROOT . '/app/models/UsuarioModel.php';
require_once ROOT . '/app/models/VeterinariaModel.php';

class UsuarioController {

    private UsuarioModel     $model;
    private VeterinariaModel $vetModel;

    public function __construct() {
        $this->requiereAutenticacion();
        $this->requiereAdmin();
        $this->model    = new UsuarioModel();
        $this->vetModel = new VeterinariaModel();
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
            'activePage'     => 'usuarios',
            'veterinarias'   => $veterinarias,
            'veterinaria_id' => $veterinaria_id,
            'usuarios'       => $this->model->getAll($cuenta_id),
            'totales'        => $this->model->getTotales($cuenta_id),
            'usuario'        => [
                'nombre' => $_SESSION['usuario_nombre'],
                'email'  => $_SESSION['usuario_email'],
                'rol'    => $_SESSION['usuario_rol'],
                'id'     => $_SESSION['usuario_id'],
            ],
            'success' => $_SESSION['flash_success'] ?? '',
            'error'   => $_SESSION['flash_error']   ?? '',
        ];
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        $this->render('usuarios/index', $datos);
    }

    public function guardar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('usuarios');
        }

        $id         = (int)($_POST['id'] ?? 0);
        $nombre     = trim($_POST['nombre'] ?? '');
        $email      = trim($_POST['email']  ?? '');
        $rol        = $_POST['rol'] ?? 'recepcion';
        $pass       = $_POST['password'] ?? '';
        $sucursal_id = (int)($_POST['sucursal_id'] ?? 0);
        $cuenta_id  = (int)($_SESSION['cuenta_id'] ?? 0);

        if (empty($nombre) || empty($email)) {
            $_SESSION['flash_error'] = 'El nombre y el correo son obligatorios.';
            $this->redirect('usuarios');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = 'El correo electrónico no es válido.';
            $this->redirect('usuarios');
        }

        if (!in_array($rol, ['admin', 'veterinario', 'recepcion'], true)) {
            $rol = 'recepcion';
        }

        if ($sucursal_id > 0 && !$this->vetModel->findById($sucursal_id, $cuenta_id)) {
            $_SESSION['flash_error'] = 'La sucursal seleccionada no es válida.';
            $this->redirect('usuarios');
        }

        if ($this->model->emailExiste($email, $id)) {
            $_SESSION['flash_error'] = 'Ya existe un usuario con ese correo.';
            $this->redirect('usuarios');
        }

        if ($id > 0) {
            $ok = $this->model->actualizar($id, compact('nombre', 'email', 'rol', 'sucursal_id'), $cuenta_id);
            $_SESSION[$ok ? 'flash_success' : 'flash_error'] = $ok
                ? 'Usuario actualizado correctamente.'
                : 'Error al actualizar el usuario.';
        } else {
            if (empty($pass) || strlen($pass) < 6) {
                $_SESSION['flash_error'] = 'La contraseña debe tener al menos 6 caracteres.';
                $this->redirect('usuarios');
            }
            $ok = $this->model->crear(['nombre' => $nombre, 'email' => $email, 'rol' => $rol,
                                       'password' => $pass, 'sucursal_id' => $sucursal_id], $cuenta_id);
            $_SESSION[$ok ? 'flash_success' : 'flash_error'] = $ok
                ? 'Usuario creado correctamente.'
                : 'Error al crear el usuario.';
        }

        $this->redirect('usuarios');
    }

    public function cambiarPassword(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('usuarios');
        }

        $id        = (int)($_POST['id'] ?? 0);
        $pass      = $_POST['password'] ?? '';
        $cuenta_id = (int)($_SESSION['cuenta_id'] ?? 0);

        if ($id <= 0 || strlen($pass) < 6) {
            $_SESSION['flash_error'] = 'La contraseña debe tener al menos 6 caracteres.';
            $this->redirect('usuarios');
        }

        $ok = $this->model->cambiarPassword($id, $pass, $cuenta_id);
        $_SESSION[$ok ? 'flash_success' : 'flash_error'] = $ok
            ? 'Contraseña actualizada correctamente.'
            : 'Error al actualizar la contraseña.';

        $this->redirect('usuarios');
    }

    public function toggle(): void {
        $id         = (int)($_GET['id'] ?? 0);
        $sesionId   = (int)($_SESSION['usuario_id'] ?? 0);
        $cuenta_id  = (int)($_SESSION['cuenta_id'] ?? 0);

        if ($id > 0 && $id !== $sesionId) {
            $this->model->toggleActivo($id, $cuenta_id);
        }
        $this->redirect('usuarios');
    }

    private function requiereAutenticacion(): void {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    private function requiereAdmin(): void {
        if (($_SESSION['usuario_rol'] ?? '') !== 'admin') {
            header('Location: ' . BASE_URL . '/dashboard');
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
