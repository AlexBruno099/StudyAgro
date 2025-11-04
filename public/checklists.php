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

// Criar nova checklist
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titulo'])) {
    $stmt = $pdo->prepare("INSERT INTO checklists (user_id, titulo, descricao) VALUES (:uid, :titulo, :desc)");
    $stmt->execute([
        'uid' => $user_id,
        'titulo' => trim($_POST['titulo']),
        'desc' => trim($_POST['descricao'])
    ]);
}

// Adicionar item
if (isset($_POST['item_descricao']) && isset($_POST['checklist_id'])) {
    $stmt = $pdo->prepare("INSERT INTO checklist_items (checklist_id, descricao) VALUES (:cid, :desc)");
    $stmt->execute([
        'cid' => $_POST['checklist_id'],
        'desc' => trim($_POST['item_descricao'])
    ]);
}

// Marcar item como concluído
if (isset($_GET['concluir_item'])) {
    $stmt = $pdo->prepare("UPDATE checklist_items SET concluido=1 WHERE id=:id");
    $stmt->execute(['id'=>$_GET['concluir_item']]);
}

// Excluir item
if (isset($_GET['excluir_item'])) {
    $stmt = $pdo->prepare("DELETE FROM checklist_items WHERE id=:id");
    $stmt->execute(['id'=>$_GET['excluir_item']]);
}

// Buscar checklists e itens
$stmt = $pdo->prepare("SELECT * FROM checklists WHERE user_id=:uid ORDER BY created_at DESC");
$stmt->execute(['uid'=>$user_id]);
$checklists = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Checklists - StudyAgro</title>
    <style>
        body { font-family: Arial, sans-serif; margin:0; display:flex; background-color:#e6ffe6; color:#0b3d0b; }
        .sidebar { background-color:#0b3d0b; width:220px; min-height:100vh; color:#a8e6a1; padding-top:20px; position:fixed; }
        .sidebar h2 { text-align:center; margin-bottom:30px; }
        .sidebar a { display:block; color:#a8e6a1; text-decoration:none; padding:12px 20px; margin:5px 0; border-radius:8px; }
        .sidebar a:hover { background-color:#4ea34e; color:#fff; }
        .main-content { margin-left:220px; padding:30px; flex:1; }
        h2 { margin-bottom:20px; }
        .checklist { background-color:#76c776; padding:15px; margin-bottom:20px; border-radius:8px; }
        .checklist h3 { margin-top:0; }
        form input, form textarea { width:100%; padding:10px; margin-bottom:10px; border:none; border-radius:6px; box-sizing:border-box; }
        form button { background-color:#4ea34e; color:#fff; padding:8px 12px; border:none; border-radius:6px; cursor:pointer; font-weight:bold; margin-top:5px; }
        .item { display:flex; justify-content:space-between; background-color:#a8e6a1; padding:8px; border-radius:6px; margin-bottom:5px; }
        .item p { margin:0; }
        .btn-excluir { background-color:#a83232; color:#fff; border:none; border-radius:5px; padding:5px 10px; cursor:pointer; }
        .btn-concluir { background-color:#0b3d0b; color:#a8e6a1; border:none; border-radius:5px; padding:5px 10px; cursor:pointer; }
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
        <h2>Meus Checklists</h2>

        <!-- Criar nova checklist -->
        <form method="POST">
            <input type="text" name="titulo" placeholder="Título da checklist" required>
            <textarea name="descricao" placeholder="Descrição (opcional)"></textarea>
            <button type="submit">Criar Checklist</button>
        </form>

        <?php foreach($checklists as $c): ?>
            <div class="checklist">
                <h3><?php echo htmlspecialchars($c['titulo']); ?></h3>
                <p><?php echo htmlspecialchars($c['descricao']); ?></p>

                <!-- Adicionar item -->
                <form method="POST">
                    <input type="hidden" name="checklist_id" value="<?php echo $c['id']; ?>">
                    <input type="text" name="item_descricao" placeholder="Adicionar item">
                    <button type="submit">Adicionar</button>
                </form>

                <!-- Listar itens -->
                <?php
                $stmt = $pdo->prepare("SELECT * FROM checklist_items WHERE checklist_id=:cid");
                $stmt->execute(['cid'=>$c['id']]);
                $itens = $stmt->fetchAll();
                ?>
                <?php foreach($itens as $i): ?>
                    <div class="item">
                        <p style="<?php echo $i['concluido'] ? 'text-decoration:line-through;' : ''; ?>"><?php echo htmlspecialchars($i['descricao']); ?></p>
                        <div>
                            <?php if(!$i['concluido']): ?>
                                <a href="?concluir_item=<?php echo $i['id']; ?>" class="btn-concluir">Concluir</a>
                            <?php endif; ?>
                            <a href="?excluir_item=<?php echo $i['id']; ?>" class="btn-excluir">Excluir</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
