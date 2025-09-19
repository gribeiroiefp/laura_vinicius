<?php
// Ligação à base de dados
$conn = mysqli_connect('127.0.0.1', 'root', '', 'livros_db');
if (!$conn) {
    die('Erro na ligação: ' . mysqli_connect_error());
}

$msg = "";

// Obter listas de autores e livros
$autores = mysqli_query($conn, "SELECT * FROM autores ORDER BY nome ASC");
$livros = mysqli_query($conn, "SELECT * FROM livros ORDER BY titulo ASC");

// Associar autor ↔ livro
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $autor_id = $_POST['autor_id'];
    $livro_id = $_POST['livro_id'];

    if ($autor_id == "" || $livro_id == "") {
        $msg = " Selecione um autor e um livro.";
    } else {
        // Verificar se já existe associação
        $check = mysqli_query($conn, "SELECT * FROM autor_livro WHERE autor_id=$autor_id AND livro_id=$livro_id");
        if (mysqli_num_rows($check) > 0) {
            $msg = " Este autor já está associado a este livro.";
        } else {
            $sql = "INSERT INTO autor_livro (autor_id, livro_id) VALUES (?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $autor_id, $livro_id);

            if (mysqli_stmt_execute($stmt)) {
                $msg = " Associação feita com sucesso!";
            } else {
                $msg = " Erro ao associar: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ligar Autor ↔ Livro</title>
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
            <a href="ligar_autor_livro.php" class="me-3 text-warning">Ligar Autor-Livro</a>
            <a href="apagar.php" class="me-3 text-white">Apagar</a>
        </nav>
    </div>
</header>

<div class="container-lg">
    <h2 class="mb-4"> Ligar Autor a Livro</h2>

    <?php if ($msg): ?>
        <div class="alert alert-info"><?= $msg ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">Associar</div>
        <div class="card-body">
            <form action="ligar_autor_livro.php" method="POST">
                <div class="mb-3">
                    <label for="autor_id" class="form-label">Selecione o Autor *</label>
                    <select name="autor_id" id="autor_id" class="form-select" required>
                        <option value="">-- Selecione --</option>
                        <?php mysqli_data_seek($autores, 0); ?>
                        <?php while ($autor = mysqli_fetch_assoc($autores)): ?>
                            <option value="<?= $autor['id'] ?>"><?= htmlspecialchars($autor['nome']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="livro_id" class="form-label">Selecione o Livro *</label>
                    <select name="livro_id" id="livro_id" class="form-select" required>
                        <option value="">-- Selecione --</option>
                        <?php mysqli_data_seek($livros, 0); ?>
                        <?php while ($livro = mysqli_fetch_assoc($livros)): ?>
                            <option value="<?= $livro['id'] ?>"><?= htmlspecialchars($livro['titulo']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-secondary">Ligar</button>
            </form>
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


