<?php

require_once ROOT . '/app/models/VeterinariaModel.php';

class ConfiguracionController {

    private VeterinariaModel $vetModel;

    public function __construct() {
        $this->requiereAutenticacion();
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
            'activePage'     => 'configuracion',
            'veterinarias'   => $veterinarias,
            'veterinaria_id' => $veterinaria_id,
            'usuario' => [
                'nombre' => $_SESSION['usuario_nombre'],
                'email'  => $_SESSION['usuario_email'],
                'rol'    => $_SESSION['usuario_rol'],
            ],
            'success' => $_SESSION['flash_success'] ?? '',
            'error'   => $_SESSION['flash_error']   ?? '',
            'tab_activa' => $_SESSION['flash_tab']  ?? null,
        ];
        unset($_SESSION['flash_success'], $_SESSION['flash_error'], $_SESSION['flash_tab']);

        $this->render('configuracion/index', $datos);
    }

    public function sucursales(): void {
        $cuenta_id      = (int)($_SESSION['cuenta_id'] ?? 0);
        $veterinarias   = $this->vetModel->getAll($cuenta_id);
        foreach ($veterinarias as &$v) {
            $v['tiene_datos'] = $this->vetModel->tieneDatos((int)$v['id']);
        }
        unset($v);
        $veterinaria_id = (int)($_SESSION['veterinaria_id'] ?? 0);
        if ($veterinaria_id === 0 && !empty($veterinarias)) {
            $veterinaria_id = (int)$veterinarias[0]['id'];
            $_SESSION['veterinaria_id'] = $veterinaria_id;
        }

        $datos = [
            'activePage'     => 'configuracion',
            'veterinarias'   => $veterinarias,
            'veterinaria_id' => $veterinaria_id,
            'usuario' => [
                'nombre' => $_SESSION['usuario_nombre'],
                'email'  => $_SESSION['usuario_email'],
                'rol'    => $_SESSION['usuario_rol'],
            ],
            'success' => $_SESSION['flash_success'] ?? '',
            'error'   => $_SESSION['flash_error']   ?? '',
        ];
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);
        $this->render('configuracion/sucursales', $datos);
    }

    public function guardarVeterinaria(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('configuracion');
        }

        $datos = $this->validarVeterinaria($_POST);
        if ($datos['error']) {
            $_SESSION['flash_error'] = $datos['error'];
            $this->redirect('configuracion');
        }

        $id        = (int)($_POST['id'] ?? 0);
        $cuenta_id = (int)($_SESSION['cuenta_id'] ?? 0);

        if ($id > 0) {
            $ok = $this->vetModel->actualizar($id, $datos, $cuenta_id);
            $_SESSION['flash_success'] = $ok ? 'Veterinaria actualizada correctamente.' : 'Error al actualizar.';
            $_SESSION['flash_tab']     = $id;
        } else {
            $nuevoId = $this->vetModel->crear($datos, $cuenta_id);
            $_SESSION['flash_success'] = $nuevoId ? 'Veterinaria agregada correctamente.' : 'Error al agregar.';
            $_SESSION['flash_tab']     = $nuevoId ?: null;
        }

        $this->redirect('configuracion');
    }

    public function eliminarVeterinaria(): void {
        $id        = (int)($_GET['id'] ?? 0);
        $cuenta_id = (int)($_SESSION['cuenta_id'] ?? 0);
        if ($id > 0) {
            if ($this->vetModel->tieneDatos($id)) {
                $_SESSION['flash_error'] = 'No se puede eliminar: la sucursal tiene ventas, ingresos, stock o usuarios asignados.';
            } else {
                $this->vetModel->eliminar($id, $cuenta_id);
                $_SESSION['flash_success'] = 'Sucursal eliminada correctamente.';
            }
        }
        $this->redirect('configuracion/sucursales');
    }

    private function validarVeterinaria(array $post): array {
        $datos = [
            'nombre'    => trim($post['nombre']    ?? ''),
            'ruc'       => trim($post['ruc']       ?? ''),
            'telefono'  => trim($post['telefono']  ?? ''),
            'email'     => trim($post['email']     ?? ''),
            'direccion' => trim($post['direccion'] ?? ''),
            'horario'   => trim($post['horario']   ?? ''),
            'sitio_web' => trim($post['sitio_web'] ?? ''),
            'error'     => '',
        ];

        if (empty($datos['nombre'])) {
            $datos['error'] = 'El nombre de la veterinaria es obligatorio.';
        } elseif (!empty($datos['email']) && !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $datos['error'] = 'El correo electrónico no es válido.';
        }

        return $datos;
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
