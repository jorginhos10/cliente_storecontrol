<?php

class Database {

    private static ?Database $instance = null;
    private mysqli $connection;

    private string $host     = 'localhost';
    private string $user     = 'jorginho_cliente_storecontrol';
    private string $password = 'jorginho10.';
    private string $dbname   = 'jorginho_cliente_storecontrol';

    private function __construct() {
        $this->connection = new mysqli($this->host, $this->user, $this->password, $this->dbname);

        if ($this->connection->connect_error) {
            http_response_code(500);
            die(json_encode(['error' => 'Error de conexión a la base de datos.']));
        }

        $this->connection->set_charset('utf8');
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): mysqli {
        return $this->connection;
    }

    private function __clone() {}
}
