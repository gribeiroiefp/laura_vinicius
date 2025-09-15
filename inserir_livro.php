<?php
// Ligação à base de dados
$conn = mysqli_connect('127.0.0.1', 'root', '', 'livros_db');
if (!$conn) {
    die('Erro na ligação: ' . mysqli_connect_error());
}

$msg = "";

// Buscar autores para o dropdown
$autores = mysqli_query($conn, "SELECT id, nome FROM autores ORDER BY nome ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $ano = trim($_POST['ano']);
    $autor_id = $_POST['autor_id'] ?? null;

    if (empty($titulo) || empty($ano) || !$autor_id) {
        $msg = "Todos os campos são obrigatórios!";
    } elseif (!isset($_FILES['capa']) || $_FILES['capa']['error'] != 0) {
        $msg = "A capa é obrigatória!";
    } else {
        // Upload da capa
        $capa_nome = time() . "_" . basename($_FILES['capa']['name']);
        $destino = "uploads/capas/" . $capa_nome;

        if (move_uploaded_file($_FILES['capa']['tmp_name'], $destino)) {
            // Inserir livro
            $sql = "INSERT INTO livros (titulo, ano, capa) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sis", $titulo, $ano, $capa_nome);

            if (mysqli_stmt_execute($stmt)) {
                $livro_id = mysqli_insert_id($conn);

                // Ligar livro ao autor
                $sql2 = "INSERT INTO autor_livro (autor_id, livro_id) VALUES (?, ?)";
                $stmt2 = mysqli_prepare($conn, $sql2);
                mysqli_stmt_bind_param($stmt2, "ii", $autor_id, $livro_id);
                mysqli_stmt_execute($stmt2);
                mysqli_stmt_close($stmt2);

                $msg = "Livro inserido com sucesso!";
            } else {
                $msg = "Erro ao inserir livro: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        } else {
            $msg = "Erro no upload da capa.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserir Livro - Autores & Livros</title>
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
            <a href="inserir_livro.php" class="me-3 text-warning">Inserir Livro</a>
            <a href="editar.php" class="me-3 text-white">Editar</a>
            <a href="ligar_autor_livro.php" class="me-3 text-white">Ligar Autor-Livro</a>
            <a href="apagar.php" class="me-3 text-white">Apagar</a>
        </nav>
    </div>
</header>

<div class="container-lg">
    <h2 class="mb-4"> Inserir Novo Livro</h2>

    <?php if ($msg): ?>
        <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <form action="inserir_livro.php" method="post" enctype="multipart/form-data" class="border p-4 rounded shadow-sm bg-light">
        <div class="mb-3">
            <label for="titulo" class="form-label">Título do Livro</label>
            <input type="text" name="titulo" id="titulo" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="ano" class="form-label">Ano</label>
            <input type="number" name="ano" id="ano" class="form-control" min="1000" max="9999" required>
        </div>

        <div class="mb-3">
            <label for="autor_id" class="form-label">Autor</label>
            <select name="autor_id" id="autor_id" class="form-select" required>
                <option value="">-- Selecione o autor --</option>
                <?php while ($autor = mysqli_fetch_assoc($autores)): ?>
                    <option value="<?= $autor['id'] ?>"><?= htmlspecialchars($autor['nome']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="capa" class="form-label">Capa do Livro</label>
            <input type="file" name="capa" id="capa" class="form-control" accept="image/*" required>
        </div>

        <button type="submit" class="btn btn-success">Salvar</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
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

