<?php
$conn = mysqli_connect('127.0.0.1', 'root', '', 'livros_db');
if (!$conn) {
    die('Erro na ligação: ' . mysqli_connect_error());
}

$msg = '';

$autores = $conn->query("SELECT id, nome FROM autores ORDER BY nome ASC")->fetch_all(MYSQLI_ASSOC);
$livros = $conn->query("SELECT id, titulo FROM livros ORDER BY titulo ASC")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $autor_id = $_POST['autor_id'];
    $livro_id = $_POST['livro_id'];

    if (!$autor_id || !$livro_id) {
        $msg = "Selecione autor e livro.";
    } else {

        $check = $conn->prepare("SELECT * FROM autor_livro WHERE autor_id=? AND livro_id=?");
        $check->bind_param("ii", $autor_id, $livro_id);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $msg = "Esta ligação já existe.";
        } else {
            $stmt = $conn->prepare("INSERT INTO autor_livro (autor_id, livro_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $autor_id, $livro_id);
            if ($stmt->execute()) {
                $msg = " Autor ligado ao livro com sucesso!";
            } else {
                $msg = " Erro: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ligar Autor a Livro</title>
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
                <a href="inserir_livro.php" class="me-3">Inserir Livro</a>
                <a href="editar.php">Editar</a>
            </nav>
        </div>
    </header>

    <main class="container-lg my-5">
        <h2>Ligar Autor a Livro</h2>

        <?php if ($msg): ?>
            <div class="alert alert-info mt-3"><?= $msg ?></div>
        <?php endif; ?>

        <form action="ligar_autor_livro.php" method="POST" class="mb-5">
            <label for="autor_id" class="form-label">Autor:</label>
            <select name="autor_id" id="autor_id" class="form-select mb-3" required>
                <option value="">-- Selecionar Autor --</option>
                <?php foreach ($autores as $autor): ?>
                    <option value="<?= $autor['id'] ?>"><?= htmlspecialchars($autor['nome']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="livro_id" class="form-label">Livro:</label>
            <select name="livro_id" id="livro_id" class="form-select mb-3" required>
                <option value="">-- Selecionar Livro --</option>
                <?php foreach ($livros as $livro): ?>
                    <option value="<?= $livro['id'] ?>"><?= htmlspecialchars($livro['titulo']) ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn btn-success">Ligar Autor ao Livro</button>
        </form>
    </main>

    <footer class="container-fluid text-center bg-light py-3 border-top">
        <div class="container-lg">
            <p>&copy; <?= date("Y") ?> Biblioteca.</p>
        </div>
    </footer>
</body>
</html>