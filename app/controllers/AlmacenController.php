<?php

require_once ROOT . '/app/models/ProductoModel.php';
require_once ROOT . '/app/models/VeterinariaModel.php';
require_once ROOT . '/app/models/IngresoModel.php';

class AlmacenController {

    private ProductoModel    $model;
    private VeterinariaModel $vetModel;

    public function __construct() {
        $this->requiereAutenticacion();
        $this->model    = new ProductoModel();
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
            'activePage'     => 'almacen',
            'veterinarias'   => $veterinarias,
            'veterinaria_id' => $veterinaria_id,
            'productos'      => $this->model->getAll($cuenta_id, $veterinaria_id),
            'totales'        => $this->model->getTotales($cuenta_id, $veterinaria_id),
            'categorias'     => $this->model->getCategorias($cuenta_id),
            'usuario'    => [
                'nombre' => $_SESSION['usuario_nombre'],
                'email'  => $_SESSION['usuario_email'],
                'rol'    => $_SESSION['usuario_rol'],
            ],
            'success' => $_SESSION['flash_success'] ?? '',
            'error'   => $_SESSION['flash_error']   ?? '',
        ];
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        $this->render('almacen/index', $datos);
    }

    public function agregar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('almacen');
        }

        $datos = $this->validarFormulario($_POST);

        if ($datos['error']) {
            $_SESSION['flash_error'] = $datos['error'];
            $this->redirect('almacen');
        }

        $id        = (int)($_GET['id'] ?? 0);
        $cuenta_id = (int)($_SESSION['cuenta_id'] ?? 0);

        $imagen = $this->procesarImagen($_FILES['imagen'] ?? null);
        if ($imagen === false) {
            $_SESSION['flash_error'] = 'La imagen debe ser JPG, PNG o WEBP y pesar menos de 3MB.';
            $this->redirect('almacen');
        }

        if ($id > 0) {
            $datos['imagen'] = $imagen ?? ($this->model->findById($id, $cuenta_id)['imagen'] ?? null);
            $ok = $this->model->actualizar($id, $datos, $cuenta_id);
            $_SESSION[$ok ? 'flash_success' : 'flash_error'] = $ok
                ? 'Producto actualizado correctamente.'
                : 'Error al actualizar el producto.';
        } else {
            $datos['imagen'] = $imagen;
            $ok = $this->model->crear($datos, $cuenta_id);
            $_SESSION[$ok ? 'flash_success' : 'flash_error'] = $ok
                ? 'Producto agregado correctamente.'
                : 'Error al guardar el producto.';
        }

        $this->redirect('almacen');
    }

    // Sube la foto del producto a assets/img/productos; retorna el nombre de archivo,
    // null si no se envió ninguna, o false si la subida no es válida.
    private function procesarImagen(?array $file): string|false|null {
        if (empty($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $permitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $mime = mime_content_type($file['tmp_name']);

        if (!isset($permitidos[$mime]) || $file['size'] > 3 * 1024 * 1024) {
            return false;
        }

        $nombre  = uniqid('prod_', true) . '.' . $permitidos[$mime];
        $destino = ROOT . '/assets/img/productos/' . $nombre;

        return move_uploaded_file($file['tmp_name'], $destino) ? $nombre : false;
    }

    public function transferir(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('almacen');
        }

        $origen_id  = (int)($_SESSION['veterinaria_id'] ?? 0);
        $destino_id = (int)($_POST['destino_id'] ?? 0);
        $cuenta_id  = (int)($_SESSION['cuenta_id'] ?? 0);
        $ids        = $_POST['producto_id']  ?? [];
        $cantidades = $_POST['cantidad']      ?? [];

        if ($destino_id <= 0 || $destino_id === $origen_id || !$this->vetModel->findById($destino_id, $cuenta_id)) {
            $_SESSION['flash_error'] = 'Selecciona una sucursal destino diferente.';
            $this->redirect('almacen');
        }

        $lineas = [];
        foreach ($ids as $i => $pid) {
            $pid  = (int)$pid;
            $cant = (int)($cantidades[$i] ?? 0);
            if ($pid > 0 && $cant > 0) {
                $lineas[] = ['producto_id' => $pid, 'cantidad' => $cant,
                             'precio_unitario' => 0, 'subtotal' => 0];
            }
        }

        if (empty($lineas)) {
            $_SESSION['flash_error'] = 'Agrega al menos un producto con cantidad mayor a 0.';
            $this->redirect('almacen');
        }

        $ingresoModel = new IngresoModel();
        $ok = $ingresoModel->transferir($origen_id, $destino_id, $lineas, (int)($_SESSION['usuario_id'] ?? 0));

        $_SESSION[$ok ? 'flash_success' : 'flash_error'] = $ok
            ? 'Stock transferido correctamente.'
            : 'Error al transferir: stock insuficiente o datos inválidos.';

        $this->redirect('almacen');
    }

    public function toggle(): void {
        $id        = (int)($_GET['id'] ?? 0);
        $cuenta_id = (int)($_SESSION['cuenta_id'] ?? 0);
        if ($id > 0) {
            $this->model->toggleActivo($id, $cuenta_id);
        }
        $this->redirect('almacen');
    }

    private function validarFormulario(array $post): array {
        $categoria_id = (int)($post['categoria_id'] ?? 0);

        $codigoBarras = trim($post['codigo_barras'] ?? '');

        $datos = [
            'nombre'        => trim($post['nombre']        ?? ''),
            'codigo'        => trim($post['codigo']        ?? ''),
            'codigo_barras' => $codigoBarras !== '' ? $codigoBarras : null,
            'descripcion'   => trim($post['descripcion']   ?? ''),
            'categoria_id'  => $categoria_id > 0 ? $categoria_id : null,
            'unidad'        => trim($post['unidad']        ?? 'unidad'),
            'precio_compra' => (float)($post['precio_compra'] ?? 0),
            'precio_venta'  => (float)($post['precio_venta']  ?? 0),
            'stock_minimo'  => (int)($post['stock_minimo'] ?? 5),
            'error'         => '',
        ];

        if (empty($datos['nombre'])) {
            $datos['error'] = 'El nombre del producto es obligatorio.';
        } elseif ($datos['precio_venta'] <= 0) {
            $datos['error'] = 'El precio de venta debe ser mayor a 0.';
        } elseif ($datos['codigo_barras'] !== null
                  && $this->model->existeCodigoBarras($datos['codigo_barras'], (int)($_SESSION['cuenta_id'] ?? 0), (int)($_GET['id'] ?? 0))) {
            $datos['error'] = 'Ese código de barra ya está asignado a otro producto.';
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
