<?php
require_once '../src/Auth.php';
session_start();

$auth = new Auth();

if ($auth->isLogged()) {
    header('Location: index.php');
    exit();
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    if ($auth->login($email, $senha)) {
        header('Location: index.php'); 
        exit();
    } else {
        $erro = "E-mail ou senha incorretos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - StudyAgro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom, #e6ffe6, #b3ffb3);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: #0b3d0b; /* verde escuro */
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            width: 350px;
            color: #fff;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #a8e6a1; /* verde claro */
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: none;
            border-radius: 8px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #76c776; /* verde médio */
            border: none;
            border-radius: 8px;
            color: #0b3d0b;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        button:hover {
            background-color: #4ea34e;
        }
        .error {
            background-color: #ffcccc;
            color: #800000;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
        }
        .link {
            text-align: center;
            margin-top: 15px;
        }
        .link a {
            color: #a8e6a1;
            text-decoration: none;
        }
        .link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>StudyAgro</h2>
        <?php if($erro) echo "<div class='error'>$erro</div>"; ?>
        <form method="POST">
            <label>Email:</label>
            <input type="email" name="email" required>
            
            <label>Senha:</label>
            <input type="password" name="senha" required>
            
            <button type="submit">Login</button>
        </form>
        <div class="link">
            <p><a href="cadastro_usuario.php">Cadastrar novo usuário</a></p>
        </div>
    </div>
</body>
</html>
