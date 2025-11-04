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
$pdo = (new Database())->pdo;

// Adicionar tarefa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titulo'])) {
    $stmt = $pdo->prepare("INSERT INTO tarefas (user_id, titulo, descricao) VALUES (:uid, :titulo, :desc)");
    $stmt->execute([
        'uid' => $user_id,
        'titulo' => trim($_POST['titulo']),
        'desc' => trim($_POST['descricao'])
    ]);
}

// Marcar tarefa como concluída
if (isset($_GET['concluir'])) {
    $stmt = $pdo->prepare("UPDATE tarefas SET status='Concluída' WHERE id=:id AND user_id=:uid");
    $stmt->execute(['id'=>$_GET['concluir'],'uid'=>$user_id]);
}

// Excluir tarefa
if (isset($_GET['excluir'])) {
    $stmt = $pdo->prepare("DELETE FROM tarefas WHERE id=:id AND user_id=:uid");
    $stmt->execute(['id'=>$_GET['excluir'],'uid'=>$user_id]);
}

// Buscar tarefas do usuário
$stmt = $pdo->prepare("SELECT * FROM tarefas WHERE user_id=:uid ORDER BY created_at DESC");
$stmt->execute(['uid'=>$user_id]);
$tarefas = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Tarefas - StudyAgro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            background-color: #e6ffe6;
            color: #0b3d0b;
        }
        .sidebar {
            background-color: #0b3d0b;
            width: 220px;
            min-height: 100vh;
            color: #a8e6a1;
            padding-top: 20px;
            position: fixed;
        }
        .sidebar h2 { text-align:center; margin-bottom:30px; }
        .sidebar a { display:block; color:#a8e6a1; text-decoration:none; padding:12px 20px; margin:5px 0; border-radius:8px; }
        .sidebar a:hover { background-color:#4ea34e; color:#fff; }
        .main-content { margin-left:220px; padding:30px; flex:1; }
        h2 { margin-bottom:20px; }
        .tarefa {
            background-color:#76c776;
            padding:15px;
            margin-bottom:10px;
            border-radius:8px;
            display:flex;
            justify-content:space-between;
            align-items:center;
        }
        .tarefa p { margin:0; }
        .btn { padding:5px 10px; border:none; border-radius:6px; cursor:pointer; font-weight:bold; }
        .btn-concluir { background-color:#4ea34e; color:#fff; margin-right:5px; }
        .btn-excluir { background-color:#a83232; color:#fff; }
        form input, form textarea { width:100%; padding:10px; margin-bottom:10px; border:none; border-radius:6px; box-sizing:border-box; }
        form button { background-color:#76c776; color:#0b3d0b; padding:10px; border:none; border-radius:8px; cursor:pointer; font-weight:bold; }
        form button:hover { background-color:#4ea34e; }
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
            <button class="btn">Sair</button>
        </form>
    </div>

    <div class="main-content">
        <h2>Minhas Tarefas</h2>

        <form method="POST">
            <input type="text" name="titulo" placeholder="Título da tarefa" required>
            <textarea name="descricao" placeholder="Descrição (opcional)"></textarea>
            <button type="submit">Adicionar Tarefa</button>
        </form>

        <?php foreach($tarefas as $tarefa): ?>
            <div class="tarefa">
                <p><strong><?php echo htmlspecialchars($tarefa['titulo']); ?></strong><br><?php echo htmlspecialchars($tarefa['descricao']); ?></p>
                <div>
                    <?php if($tarefa['status'] != 'Concluída'): ?>
                        <a href="?concluir=<?php echo $tarefa['id']; ?>" class="btn btn-concluir">Concluir</a>
                    <?php endif; ?>
                    <a href="?excluir=<?php echo $tarefa['id']; ?>" class="btn btn-excluir">Excluir</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
