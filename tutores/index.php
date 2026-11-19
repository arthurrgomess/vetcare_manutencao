<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../config/auth.php';
exigirLogin();

$titulo = 'Tutores';
$busca = trim($_GET['busca'] ?? '');

if ($busca === '') {
    $sql = "SELECT * FROM tutores";
    $param = [];
} else {
    $sql = "SELECT * FROM tutores WHERE nome LIKE ? OR email LIKE ? OR telefone LIKE ?";
    $param = ["%$busca%", "%$busca%", "%$busca%"];
}

$stmt = $pdo->prepare($sql . " ORDER BY nome");
$stmt->execute($param);
$rows = $stmt->fetchAll();

include __DIR__.'/../includes/header.php';
?>
<h1>Tutores</h1>
<p class="muted">Cadastro e consulta de responsáveis pelos animais.</p>

<?php if (($_GET['erro'] ?? '') === 'vinculo'): ?>
    <div class="alert">Não é possível excluir este tutor pois existem animais vinculados a ele.</div>
<?php elseif (($_GET['msg'] ?? '') === 'excluido'): ?>
    <div class="alert success">Tutor excluído com sucesso.</div>
<?php endif; ?>

<div class="actions"><a class="btn" href="form.php">+ Novo tutor</a></div>

<section class="panel">
    <form method="get">
        <div class="field">
            <label>Buscar tutor</label>
            <input name="busca" placeholder="Digite o nome, telefone ou e-mail" value="<?= htmlspecialchars($busca) ?>">
        </div>
        <button>Buscar</button>
        <?php if ($busca !== ''): ?>
            <a class="btn secondary" href="index.php">Limpar</a>
        <?php endif; ?>
    </form>
</section>

<div class="panel table-wrap">
    <table>
        <tr>
            <th>Nome</th>
            <th>Telefone</th>
            <th>E-mail</th>
            <th>Estado</th>
            <th>Ações</th>
        </tr>
        <?php if (!$rows): ?>
            <tr><td colspan="5" class="empty">Nenhum tutor encontrado.</td></tr>
        <?php endif; ?>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['nome']) ?></td>
                <td><?= htmlspecialchars($r['telefone']) ?></td>
                <td><?= htmlspecialchars($r['email']) ?></td>
                <td><?= $r['ativo'] ? 'Ativo' : 'Inativo' ?></td>
                <td>
                    <a href="form.php?id=<?= $r['id'] ?>">Editar</a> |
                    <a href="excluir.php?id=<?= $r['id'] ?>" onclick="return confirm('Deseja realmente excluir este tutor?')">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php include __DIR__.'/../includes/footer.php'; ?>