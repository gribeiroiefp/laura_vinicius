<?php
$conn = mysqli_connect('127.0.0.1', 'root', '', 'livros_db');
if (!$conn) {
    die('Erro na ligação: ' . mysqli_connect_error());
}

$msg = '';

$autores = [];
$sql = "SELECT id, nome FROM autores ORDER BY nome ASC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $autores = $result->fetch_all(MYSQLI_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = trim($_POST['titulo']);
    $ano_publicacao = $_POST['ano_publicacao'] ?: null;
    $autor_id = $_POST['autor_id'];

    if (!$titulo || !$autor_id) {
        $msg = "Preencha todos os campos obrigatórios.";
    } else {
        $diretorio = "uploads/livros/";
        if (!is_dir($diretorio)) mkdir($diretorio, 0755, true);

        $capa = $_FILES['capa']['name'];
        $caminho = $diretorio . basename($capa);

        if (move_uploaded_file($_FILES['capa']['tmp_name'], $caminho)) {
            $sql = "INSERT INTO livros (titulo, ano_publicacao, autor_id, capa) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("siis", $titulo, $ano_publicacao, $autor_id, $capa);

            if ($stmt->execute()) {
                $msg = " Livro inserido com sucesso!";
            } else {
                $msg = "Erro ao inserir livro: " . $conn->error;
            }
        } else {
            $msg = "Erro ao carregar a capa.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
    <header class="container-fluid bg-light py-3 border-bottom">
        <div class="container-lg d-flex justify-content-between align-items-center">
            <h1 class="h3"> Biblioteca</h1>
            <nav>
                <a href="index.php" class="me-3">Página inicial</a>
                <a href="inserir_autor.php" class="me-3">Inserir Autor</a>
                <a href="editar.php" class="me-3">Editar</a>
                <a href="ligar_autor_livro.php">Ligar Autor-Livro</a>
            </nav>
        </div>
    </header>

    <main class="container-lg my-5">
        <h2>Inserir Novo Livro</h2>

        <?php if ($msg): ?>
            <div class="alert alert-info mt-3"><?= $msg ?></div>
        <?php endif; ?>

        <form action="inserir_livro.php" method="POST" enctype="multipart/form-data" class="mb-5">
            <input type="text" name="titulo" placeholder="Título" required class="form-control mb-3" />
            <input type="number" name="ano_publicacao" placeholder="Ano de publicação" class="form-control mb-3" />
            
            <label for="autor_id" class="form-label">Autor:</label>
            <select name="autor_id" id="autor_id" required class="form-select mb-3">
                <option value="">-- Selecionar Autor --</option>
                <?php foreach ($autores as $autor): ?>
                    <option value="<?= $autor['id'] ?>"><?= htmlspecialchars($autor['nome']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="capa" class="form-label">Capa do livro:</label>
            <input type="file" name="capa" id="capa" accept="image/*" required class="form-control mb-3" />

            <button type="submit" class="btn btn-primary">Inserir Livro</button>
        </form>
    </main>

    <footer class="container-fluid text-center bg-light py-3 border-top">
        <div class="container-lg">
            <p>&copy; <?= date("Y") ?> Biblioteca.</p>
        </div>
    </footer>
</body>
</html>