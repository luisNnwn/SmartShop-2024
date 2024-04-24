<?php

use Codeception\Lib\Driver\Db;
use PHPUnit\Framework\TestCase;

class InsertUserTest extends TestCase
{
    protected $db;

    protected function setUp(): void
    {
        // Configurar y conectar a la base de datos
        $dsn = 'mysql:host=http://localhost/SmartShop-2024/;dbname=shop_db';
        $user = 'root';
        $password = '';
        $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
        $this->db = new Db($dsn, $user, $password, $options);
    }

    public function testInsertUser()
    {
        // Datos de ejemplo para insertar
        $userData = [
            'nombre' => 'John Doe',
            'correo' => 'john@example.com',
        ];

        // Query de inserción
        $query = $this->db->insert('usuarios', $userData);

        // Ejecutar la query
        $this->db->executeQuery($query, array_values($userData));

        // Verificar si el usuario se ha insertado correctamente
        $userId = $this->db->lastInsertId('usuarios');
        $this->assertNotNull($userId);

        // Opcional: Verificar si el usuario realmente existe en la base de datos
        $result = $this->db->getDbh()->query("SELECT * FROM usuarios WHERE id = $userId")->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals('John Doe', $result['nombre']);
        $this->assertEquals('john@example.com', $result['correo']);
    }
}

//php vendor/bin/codecept run Unit baseTest