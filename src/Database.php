<?php
class Database {
 private $host = 'localhost';
private $port = 3307;
private $db   = 'studyagro';
private $user = 'alexB';
private $pass = 'masterkey';
private $charset = 'utf8mb4';
    public $pdo;

    public function __construct() {
        $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            die("Erro de conexão: " . $e->getMessage());
        }
    }
}
