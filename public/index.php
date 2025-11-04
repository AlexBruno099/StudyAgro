<?php
require_once '../src/Auth.php';
require_once '../src/Database.php';
session_start();

$auth = new Auth();
if (!$auth->isLogged()) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$nome = $_SESSION['user_nome'];

$pdo = (new Database())->pdo;

// Resumo de tarefas
$stmt = $pdo->prepare("SELECT COUNT(*) FROM tarefas WHERE user_id = :uid AND status != 'Concluída'");
$stmt->execute(['uid' => $user_id]);
$pendentes = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM tarefas WHERE user_id = :uid AND status = 'Concluída'");
$stmt->execute(['uid' => $user_id]);
$concluidas = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - StudyAgro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            background-color: #e6ffe6;
            color: #0b3d0b;
        }
        /* Sidebar */
        .sidebar {
            background-color: #0b3d0b;
            width: 220px;
            min-height: 100vh;
            color: #a8e6a1;
            padding-top: 20px;
            position: fixed;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        .sidebar a {
            display: block;
            color: #a8e6a1;
            text-decoration: none;
            padding: 12px 20px;
            margin: 5px 0;
            border-radius: 8px;
        }
        .sidebar a:hover {
            background-color: #4ea34e;
            color: #fff;
        }

        /* Main content */
        .main-content {
            margin-left: 220px;
            padding: 30px;
            flex: 1;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #1f5e1f;
            padding: 15px 30px;
            border-radius: 10px;
            margin-bottom: 20px;
            color: #a8e6a1;
        }

        .logout {
            background-color: #a8e6a1;
            color: #0b3d0b;
            padding: 8px 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        .logout:hover {
            background-color: #76c776;
        }

        /* Cards de resumo */
        .cards {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .card {
            background-color: #76c776;
            color: #0b3d0b;
            border-radius: 10px;
            padding: 20px;
            width: 200px;
            text-align: center;
            box-shadow: 0 3px 8px rgba(0,0,0,0.2);
            transition: transform 0.2s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card h3 {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>StudyAgro</h2>
        <a href="index.php">Dashboard</a>
        <a href="tarefas.php">Tarefas</a>
        <a href="materiais.php">Materiais</a>
        <a href="checklists.php">Checklists</a>
        <form method="POST" action="logout.php" style="margin-top: 20px; text-align:center;">
            <button class="logout">Sair</button>
        </form>
    </div>

    <div class="main-content">
        <header>
            <h2>Bem-vindo, <?php echo htmlspecialchars($nome); ?>!</h2>
        </header>

        <h3>Resumo rápido</h3>
        <div class="cards">
            <div class="card">
                <h3>Tarefas Pendentes</h3>
                <p><?php echo $pendentes; ?></p>
            </div>
            <div class="card">
                <h3>Tarefas Concluídas</h3>
                <p><?php echo $concluidas; ?></p>
            </div>
            <!-- Podemos adicionar cards de Materiais e Checklists aqui -->
        </div>
    </div>
</body>
</html>
