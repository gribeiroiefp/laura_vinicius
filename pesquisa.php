<?php
// Ligação à base de dados
$conn = mysqli_connect('127.0.0.1', 'root', '', 'livros_db');
if (!$conn) {
    die('Erro na ligação: ' . mysqli_connect_error());
}

$resultados = [];
$termo = "";

// Se o utilizador enviou a pesquisa
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['q'])) {
    $termo = trim($_GET['q']);

    if ($termo !== "") {
        $sql = "
            SELECT livros.id, livros.titulo, livros.ano, livros.capa, autores.nome AS autor_nome
            FROM livros
            LEFT JOIN autor_livro ON livros.id = autor_livro.livro_id
            LEFT JOIN autores ON autores.id = autor_livro.autor_id
            WHERE livros.titulo LIKE ? OR autores.nome LIKE ?
            ORDER BY livros.titulo ASC
        ";

        $stmt = mysqli_prepare($conn, $sql);
        $like = "%" . $termo . "%";
        mysqli_stmt_bind_param($stmt, "ss", $like, $like);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($res)) {
            $resultados[] = $row;
        }

        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesquisa - Autores & Livros</title>
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
            <a href="pesquisa.php" class="me-3 text-warning">Pesquisa</a>
            <a href="inserir_autor.php" class="me-3 text-white">Inserir Autor</a>
            <a href="inserir_livro.php" class="me-3 text-white">Inserir Livro</a>
            <a href="editar.php" class="me-3 text-white">Editar</a>
            <a href="ligar_autor_livro.php" class="me-3 text-white">Ligar Autor-Livro</a>
            <a href="apagar.php" class="me-3 text-white">Apagar</a>
        </nav>
    </div>
</header>

<div class="container-lg">
    <h2 class="mb-4"> Pesquisa de Livros e Autores</h2>

    <form action="pesquisa.php" method="get" class="mb-4">
        <div class="input-group">
            <input type="text" name="q" class="form-control" placeholder="Digite o título do livro ou nome do autor"
                   value="<?= htmlspecialchars($termo) ?>" required>
            <button type="submit" class="btn btn-secondary">Pesquisar</button>
        </div>
    </form>

    <?php if ($_SERVER["REQUEST_METHOD"] == "GET" && $termo !== ""): ?>
        <h3 class="mb-3">Resultados para: <em><?= htmlspecialchars($termo) ?></em></h3>
        <?php if (count($resultados) > 0): ?>
            <div class="row">
                <?php foreach ($resultados as $res): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <img src="./uploads/capas/<?= htmlspecialchars($res['capa']) ?>" 
                                 class="card-img-top" 
                                 alt="<?= htmlspecialchars($res['titulo']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($res['titulo']) ?></h5>
                                <p class="card-text">Ano: <?= htmlspecialchars($res['ano']) ?></p>
                                <p class="card-text"><strong>Autor:</strong> <?= htmlspecialchars($res['autor_nome'] ?? "Desconhecido") ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="alert alert-warning">Nenhum resultado encontrado.</p>
        <?php endif; ?>
    <?php endif; ?>
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

