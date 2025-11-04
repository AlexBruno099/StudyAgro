<?php
require_once 'Database.php';

class User {
    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->pdo;
    }

    public function create($nome, $email, $senha) {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (nome, email, senha_hash) VALUES (?, ?, ?)");
        return $stmt->execute([$nome, $email, $senha_hash]);
    }

    public function getByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
}
