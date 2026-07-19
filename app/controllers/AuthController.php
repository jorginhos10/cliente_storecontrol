<?php

require_once ROOT . '/app/models/UsuarioModel.php';

class AuthController {

    private UsuarioModel $model;

    public function __construct() {
        $this->model = new UsuarioModel();
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
