<?php
 
$conn = mysqli_connect('127.0.0.1', 'root', '', 'livros_db');
if (!$conn) {
    die('Erro na ligação: ' . mysqli_connect_error());
}
 
$livros = mysqli_query($conn, "SELECT id, titulo, ano, capa FROM livros ORDER BY ano DESC LIMIT 3");
 
$autores = mysqli_query($conn, "
    SELECT autores.id, autores.nome, autores.foto, autores.nacionalidade, COUNT(autor_livro.livro_id) AS total
    FROM autores
    JOIN autor_livro ON autores.id = autor_livro.autor_id
    GROUP BY autores.id
    ORDER BY total DESC
    LIMIT 3;
");
 
?>
 
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autores & Livros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/style.css">
</head>
 
<body>
 
    <header class="container-fluid bg-light py-3 mb-4 shadow-sm">
        <div class="container-lg d-flex justify-content-between align-items-center">
            <h1>Autores & Livros</h1>
            <nav>
                <a href="index.php" class="me-3">Página inicial</a>
                <a href="pesquisa.php">Pesquisa</a>
                <a href="inserir_ator.php" class="me-3">Inserir Autor</a>
                <a href="inserir_livro.php" class="me-3">Inserir Livro</a>
                <a href="editar.php" class="me-3">Editar</a>
                <a href="ligar_autor_livro.php">Ligar Autor-Livro</a>
            </nav>
        </div>
    </header>
 
    <div class="container-lg">
 
        <h2 class="mb-3">Últimos 3 livros</h2>
        <div class="row mb-5">
            <?php if ($livros && mysqli_num_rows($livros) > 0): ?>
                <?php while ($livro = mysqli_fetch_assoc($livros)): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <img src="./upload/capas/<?= htmlspecialchars($livro['capa']) ?>" class="card-img-top" alt="<?= htmlspecialchars($livro['titulo']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($livro['titulo']) ?></h5>
                                <p class="card-text">Ano: <?= htmlspecialchars($livro['ano']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Nenhum livro encontrado.</p>
            <?php endif; ?>
        </div>
 
        <h2 class="mb-3">Autores com mais livros</h2>
        <div class="row">
            <?php if ($autores && mysqli_num_rows($autores) > 0): ?>
                <?php while ($autor = mysqli_fetch_assoc($autores)): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 text-center shadow-sm">
                            <img src="./upload/fotos/<?= htmlspecialchars($autor['foto']) ?>" class="card-img-top mx-auto mt-3 rounded-circle" style="width:150px; height:150px; object-fit:cover;" alt="<?= htmlspecialchars($autor['nome']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($autor['nome']) ?></h5>
                                <p class="card-text">Nacionalidade: <?= htmlspecialchars($autor['nacionalidade']) ?></p>
                                <p class="card-text">Livros: <?= htmlspecialchars($autor['total']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Nenhum autor encontrado.</p>
            <?php endif; ?>
        </div>
    </div>
 
    <footer class="container-fluid text-center py-3 mt-5 bg-light border-top">
        <div class="container-lg">
            <p>&copy; 2025 Autores & Livros.</p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
 
</body>
</html>
 
<?php mysqli_close($conn); ?>