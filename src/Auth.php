<?php
require_once 'Database.php';

class Auth {
    private $pdo;

    public function __construct() {
        $this->pdo = (new Database())->pdo;
    }

    public function login($email, $senha) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($senha, $user['senha_hash'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nome'] = $user['nome'];
            return true;
        }
        return false;
    }

    public function isLogged() {
        return isset($_SESSION['user_id']);
    }

    public function logout() {
        session_start();
        session_destroy();
    }
}
