<?php

require_once ROOT . '/app/models/UsuarioModel.php';
require_once ROOT . '/app/models/CuentaModel.php';

class AuthController {

    private UsuarioModel $model;
    private CuentaModel  $cuentaModel;

    public function __construct() {
        $this->model       = new UsuarioModel();
        $this->cuentaModel = new CuentaModel();
    }

    public function login(): void {
        if ($this->estaAutenticado()) {
            $this->redirect('dashboard');
        }

        $datos = ['error' => '', 'email' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $resultado = $this->procesarLogin();
            if ($resultado['exitoso']) {
                $this->redirect('dashboard');
            }
            $datos['error'] = $resultado['error'];
            $datos['email'] = $resultado['email'];
        }

        $this->render('auth/login', $datos);
    }

    public function registro(): void {
        if ($this->estaAutenticado()) {
            $this->redirect('dashboard');
        }

        $datos = [
            'error' => '', 'negocio' => '', 'nombre' => '', 'email' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $resultado = $this->procesarRegistro();
            if ($resultado['exitoso']) {
                $this->redirect('dashboard');
            }
            $datos['error']   = $resultado['error'];
            $datos['negocio'] = $resultado['negocio'];
            $datos['nombre']  = $resultado['nombre'];
            $datos['email']   = $resultado['email'];
        }

        $this->render('auth/registro', $datos);
    }

    public function logout(): void {
        $_SESSION = [];
        session_destroy();
        $this->redirect('');
    }

    private function procesarLogin(): array {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            return ['exitoso' => false, 'error' => 'Por favor completa todos los campos.', 'email' => $email];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['exitoso' => false, 'error' => 'El correo electrónico no es válido.', 'email' => $email];
        }

        $usuario = $this->model->findByEmail($email);

        if ($usuario && $this->model->verificarPassword($password, $usuario['password'])) {
            $_SESSION['usuario_id']     = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_email']  = $usuario['email'];
            $_SESSION['usuario_rol']    = $usuario['rol'];
            $_SESSION['cuenta_id']      = (int)$usuario['cuenta_id'];
            $_SESSION['sucursal_fija']  = (int)($usuario['sucursal_id'] ?? 0) > 0;

            if (!empty($usuario['sucursal_id'])) {
                // Empleado con sucursal asignada — fijar y no permitir cambio
                $_SESSION['veterinaria_id'] = (int)$usuario['sucursal_id'];
            } else {
                // Admin u otro sin restricción — usar última selección
                $_SESSION['veterinaria_id'] = (int)($usuario['ultima_veterinaria'] ?? 0);
            }
            return ['exitoso' => true, 'error' => '', 'email' => $email];
        }

        return ['exitoso' => false, 'error' => 'Correo o contraseña incorrectos.', 'email' => $email];
    }

    private function procesarRegistro(): array {
        $negocio  = trim($_POST['negocio']  ?? '');
        $nombre   = trim($_POST['nombre']   ?? '');
        $email    = trim($_POST['email']    ?? '');
        $password = $_POST['password']         ?? '';
        $password2 = $_POST['password2']       ?? '';

        $datos = ['negocio' => $negocio, 'nombre' => $nombre, 'email' => $email];

        if (empty($negocio) || empty($nombre) || empty($email) || empty($password)) {
            return ['exitoso' => false, 'error' => 'Por favor completa todos los campos.'] + $datos;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['exitoso' => false, 'error' => 'El correo electrónico no es válido.'] + $datos;
        }

        if (strlen($password) < 6) {
            return ['exitoso' => false, 'error' => 'La contraseña debe tener al menos 6 caracteres.'] + $datos;
        }

        if ($password !== $password2) {
            return ['exitoso' => false, 'error' => 'Las contraseñas no coinciden.'] + $datos;
        }

        if ($this->model->emailExiste($email)) {
            return ['exitoso' => false, 'error' => 'Ya existe una cuenta registrada con ese correo.'] + $datos;
        }

        $resultado = $this->cuentaModel->crearCuentaConAdmin($negocio, $nombre, $email, $password);
        if (!$resultado) {
            return ['exitoso' => false, 'error' => 'Error al crear la cuenta. Intenta nuevamente.'] + $datos;
        }

        $_SESSION['usuario_id']     = $resultado['usuario_id'];
        $_SESSION['usuario_nombre'] = $nombre;
        $_SESSION['usuario_email']  = $email;
        $_SESSION['usuario_rol']    = 'admin';
        $_SESSION['cuenta_id']      = $resultado['cuenta_id'];
        $_SESSION['sucursal_fija']  = false;
        $_SESSION['veterinaria_id'] = $resultado['veterinaria_id'];

        return ['exitoso' => true, 'error' => ''] + $datos;
    }

    private function estaAutenticado(): bool {
        return isset($_SESSION['usuario_id']);
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
