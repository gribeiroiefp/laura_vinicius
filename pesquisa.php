<?php
$conn = mysqli_connect('127.0.0.1', 'root', '', 'livros_db');
if (!$conn) {
    die('Erro na ligação: ' . mysqli_connect_error());
}
 
$resultados_autores = [];
$resultados_livros = [];
$termo = "";
 
if ($_SERVER["REQUEST_METHOD"] === "GET" && !empty($_GET['q'])) {
    $termo = trim($_GET['q']);
 
    // Pesquisar autores
    $stmt_autor = $conn->prepare("SELECT * FROM autores WHERE nome LIKE ? OR nacionalidade LIKE ?");
    $like = "%" . $termo . "%";
    $stmt_autor->bind_param("ss", $like, $like);
    $stmt_autor->execute();
    $resultados_autores = $stmt_autor->get_result();
 
    // Pesquisar livros
    $stmt_livro = $conn->prepare("SELECT * FROM livros WHERE titulo LIKE ? OR ano LIKE ?");
    $stmt_livro->bind_param("ss", $like, $like);
    $stmt_livro->execute();
    $resultados_livros = $stmt_livro->get_result();
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>Pesquisar</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<header class="container-fluid bg-light py-3 mb-4 shadow-sm">
<div class="container-lg d-flex justify-content-between align-items-center">
<h1> Pesquisa</h1>
<nav>
<a href="index.php" class="me-3">Página inicial</a>
<a href="inserir_ator.php" class="me-3">Inserir Autor</a>
<a href="inserir_livro.php" class="me-3">Inserir Livro</a>
<a href="editar.php" class="me-3">Editar</a>
<a href="ligar_autor_livro.php">Ligar Autor-Livro</a>
</nav>
</div>
</header>
 
<div class="container-lg">
<form method="GET" action="pesquisa.php" class="input-group mb-4">
<input type="text" name="q" class="form-control" placeholder="Pesquisar autores ou livros..." value="<?= htmlspecialchars($termo) ?>">
<button class="btn btn-primary" type="submit">Pesquisar</button>
</form>
 
    <?php if (!empty($termo)): ?>
<h2 class="mb-3">Resultados da pesquisa: "<?= htmlspecialchars($termo) ?>"</h2>
 
        <!-- Resultados Autores -->
<h3 class="mt-4">Autores</h3>
<div class="row">
<?php if ($resultados_autores && $resultados_autores->num_rows > 0): ?>
<?php while ($autor = $resultados_autores->fetch_assoc()): ?>
<div class="col-md-4 mb-4">
<div class="card h-100 text-center shadow-sm">
<img src="uploads/fotos/<?= htmlspecialchars($autor['foto']) ?>" 
                                 class="card-img-top mx-auto mt-3 rounded-circle" 
                                 style="width:150px; height:150px; object-fit:cover;" 
                                 alt="<?= htmlspecialchars($autor['nome']) ?>">
<div class="card-body">
<h5 class="card-title"><?= htmlspecialchars($autor['nome']) ?></h5>
<p class="card-text">Nacionalidade: <?= htmlspecialchars($autor['nacionalidade']) ?></p>
<p class="card-text">Nascimento: <?= htmlspecialchars($autor['data_nascimento']) ?></p>
</div>
</div>
</div>
<?php endwhile; ?>
<?php else: ?>
<p>Nenhum autor encontrado.</p>
<?php endif; ?>
</div>
 
        <!-- Resultados Livros -->
<h3 class="mt-4">Livros</h3>
<div class="row">
<?php if ($resultados_livros && $resultados_livros->num_rows > 0): ?>
<?php while ($livro = $resultados_livros->fetch_assoc()): ?>
<div class="col-md-4 mb-4">
<div class="card h-100 shadow-sm">
<img src="uploads/capas/<?= htmlspecialchars($livro['capa']) ?>" 
                                 class="card-img-top" 
                                 style="height:250px; object-fit:cover;" 
                                 alt="<?= htmlspecialchars($livro['titulo']) ?>">
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
<?php endif; ?>
</div>
 
<footer class="container-fluid text-center py-3 mt-5 bg-light border-top">
<div class="container-lg">
<p>&copy; <?= date("Y") ?> Autores & Livros.</p>
</div>
</footer>
</body>
</html>
 
<?php mysqli_close($conn); ?>