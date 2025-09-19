<?php
// Ligação à base de dados
$conn = mysqli_connect('127.0.0.1', 'root', '', 'livros_db');
if (!$conn) {
    die('Erro na ligação: ' . mysqli_connect_error());
}

$msg = "";

// Se foi pedido para excluir (com confirmação)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tipo'], $_POST['id'])) {
    $tipo = $_POST['tipo']; // "autor" ou "livro"
    $id = (int) $_POST['id'];

    if ($tipo === "autor") {
        // Apagar associações primeiro
        mysqli_query($conn, "DELETE FROM autor_livro WHERE autor_id = $id");
        $sql = "DELETE FROM autores WHERE id = $id";
    } elseif ($tipo === "livro") {
        mysqli_query($conn, "DELETE FROM autor_livro WHERE livro_id = $id");
        $sql = "DELETE FROM livros WHERE id = $id";
    }

    if (isset($sql) && mysqli_query($conn, $sql)) {
        $msg = ucfirst($tipo) . " apagado com sucesso!";
    } else {
        $msg = "Erro ao apagar " . $tipo . ": " . mysqli_error($conn);
    }
}

// Listar autores e livros
$autores = mysqli_query($conn, "SELECT id, nome, foto FROM autores ORDER BY nome ASC");
$livros = mysqli_query($conn, "SELECT id, titulo, capa FROM livros ORDER BY titulo ASC");
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apagar - Autores & Livros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>

<header class="container-fluid bg-dark text-white py-3 mb-4 shadow-sm">
    <div class="container-lg d-flex justify-content-between align-items-center">
        <h1>Autores & Livros</h1>
        <nav>
            <a href="index.php" class="me-3 text-white">Página inicial</a>
            <a href="pesquisa.php" class="me-3 text-white">Pesquisa</a>
            <a href="inserir_autor.php" class="me-3 text-white">Inserir Autor</a>
            <a href="inserir_livro.php" class="me-3 text-white">Inserir Livro</a>
            <a href="editar.php" class="me-3 text-white">Editar</a>
            <a href="ligar_autor_livro.php" class="me-3 text-white">Ligar Autor-Livro</a>
            <a href="apagar.php" class="me-3 text-warning">Apagar</a>
        </nav>
    </div>
</header>

<div class="container-lg">
    <h2 class="mb-4"> Apagar Autores & Livros</h2>

    <?php if ($msg): ?>
        <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Apagar Autores -->
        <div class="col-md-6">
            <h3>Autores</h3>
            <?php if ($autores && mysqli_num_rows($autores) > 0): ?>
                <ul class="list-group">
                    <?php while ($autor = mysqli_fetch_assoc($autores)): ?>
                        <li class="list-group-item d-flex align-items-center justify-content-between">
                            <div>
                                <img src="./uploads/fotos/<?= htmlspecialchars($autor['foto']) ?>" 
                                     alt="<?= htmlspecialchars($autor['nome']) ?>" 
                                     style="width:40px; height:40px; object-fit:cover; border-radius:50%; margin-right:10px;">
                                <?= htmlspecialchars($autor['nome']) ?>
                            </div>
                            <form action="apagar.php" method="post" onsubmit="return confirm('Tem certeza que deseja apagar este autor?');">
                                <input type="hidden" name="tipo" value="autor">
                                <input type="hidden" name="id" value="<?= $autor['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Apagar</button>
                            </form>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>Nenhum autor encontrado.</p>
            <?php endif; ?>
        </div>

        <!-- Apagar Livros -->
        <div class="col-md-6">
            <h3>Livros</h3>
            <?php if ($livros && mysqli_num_rows($livros) > 0): ?>
                <ul class="list-group">
                    <?php while ($livro = mysqli_fetch_assoc($livros)): ?>
                        <li class="list-group-item d-flex align-items-center justify-content-between">
                            <div>
                                <img src="./uploads/capas/<?= htmlspecialchars($livro['capa']) ?>" 
                                     alt="<?= htmlspecialchars($livro['titulo']) ?>" 
                                     style="width:40px; height:40px; object-fit:cover; margin-right:10px;">
                                <?= htmlspecialchars($livro['titulo']) ?>
                            </div>
                            <form action="apagar.php" method="post" onsubmit="return confirm('Tem certeza que deseja apagar este livro?');">
                                <input type="hidden" name="tipo" value="livro">
                                <input type="hidden" name="id" value="<?= $livro['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Apagar</button>
                            </form>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>Nenhum livro encontrado.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<footer class="container-fluid text-center py-3 mt-5 bg-dark text-white border-top">
    <div class="container-lg">
        <p>&copy; 2025 Autores & Livros | Desenvolvido Laura & Vinicius</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>
