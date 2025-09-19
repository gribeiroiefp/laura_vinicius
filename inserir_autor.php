<?php
// Ligação à base de dados
$conn = mysqli_connect('127.0.0.1', 'root', '', 'livros_db');
if (!$conn) {
    die('Erro na ligação: ' . mysqli_connect_error());
}

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $data_nascimento = $_POST['data_nascimento'] ?? null;
    $nacionalidade = trim($_POST['nacionalidade']);

    if (empty($nome) || empty($nacionalidade) || empty($data_nascimento)) {
        $msg = "Todos os campos são obrigatórios!";
    } elseif (!isset($_FILES['foto']) || $_FILES['foto']['error'] != 0) {
        $msg = "A foto é obrigatória!";
    } else {
        // Upload da foto
        $foto_nome = time() . "_" . basename($_FILES['foto']['name']);
        $destino = "upload/fotos/" . $foto_nome;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
            $sql = "INSERT INTO autores (nome, data_nascimento, nacionalidade, foto) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssss", $nome, $data_nascimento, $nacionalidade, $foto_nome);

            if (mysqli_stmt_execute($stmt)) {
                $msg = "Autor inserido com sucesso!";
            } else {
                $msg = "Erro ao inserir autor: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        } else {
            $msg = "Erro no upload da foto.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserir Autor - Autores & Livros</title>
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
            <a href="inserir_autor.php" class="me-3 text-warning">Inserir Autor</a>
            <a href="inserir_livro.php" class="me-3 text-white">Inserir Livro</a>
            <a href="editar.php" class="me-3 text-white">Editar</a>
            <a href="ligar_autor_livro.php" class="me-3 text-white">Ligar Autor-Livro</a>
            <a href="apagar.php" class="me-3 text-white">Apagar</a>
        </nav>
    </div>
</header>

<div class="container-lg">
    <h2 class="mb-4"> Inserir Novo Autor</h2>

    <?php if ($msg): ?>
        <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <form action="inserir_autor.php" method="post" enctype="multipart/form-data" class="border p-4 rounded shadow-sm bg-light">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome do Autor</label>
            <input type="text" name="nome" id="nome" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="data_nascimento" class="form-label">Data de Nascimento</label>
            <input type="date" name="data_nascimento" id="data_nascimento" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="nacionalidade" class="form-label">Nacionalidade</label>
            <input type="text" name="nacionalidade" id="nacionalidade" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="foto" class="form-label">Foto do Autor</label>
            <input type="file" name="foto" id="foto" class="form-control" accept="image/*" required>
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


