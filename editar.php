<?php
$conn = mysqli_connect('127.0.0.1', 'root', '', 'livros_db');
if (!$conn) {
    die('Erro na ligação: ' . mysqli_connect_error());
}

$msg = '';

if (isset($_POST['editar_autor'])) {
    $id = $_POST['id'];
    $nome = trim($_POST['nome']);
    $data_nascimento = $_POST['data_nascimento'] ?: null;
    $nacionalidade = $_POST['nacionalidade'];
    $foto = null;

    if (!$nome) {
        $msg = " O nome do autor é obrigatório.";
    } else {
        if (!empty($_FILES['foto']['name'])) {
            $diretorio = "uploads/autores/";
            if (!is_dir($diretorio)) mkdir($diretorio, 0755, true);

            $foto = basename($_FILES['foto']['name']);
            $caminho = $diretorio . $foto;

            if (!move_uploaded_file($_FILES['foto']['tmp_name'], $caminho)) {
                $msg = " Erro ao carregar foto do autor.";
            }
        }

        $sql = "UPDATE autores SET nome=?, data_nascimento=?, nacionalidade=?";
        if ($foto) $sql .= ", foto=?";
        $sql .= " WHERE id=?";

        $stmt = $conn->prepare($sql);

        if ($foto) {
            $stmt->bind_param("ssssi", $nome, $data_nascimento, $nacionalidade, $foto, $id);
        } else {
            $stmt->bind_param("sssi", $nome, $data_nascimento, $nacionalidade, $id);
        }

        if ($stmt->execute()) {
            $msg = " Autor atualizado com sucesso!";
        } else {
            $msg = " Erro ao atualizar autor: " . $conn->error;
        }
    }
}

if (isset($_POST['editar_livro'])) {
    $id = $_POST['id'];
    $titulo = trim($_POST['titulo']);
    $ano_publicacao = $_POST['ano_publicacao'] ?: null;
    $autor_id = $_POST['autor_id'];
    $capa = null;

    if (!$titulo || !$autor_id) {
        $msg = " Preencha os campos obrigatórios.";
    } else {
        if (!empty($_FILES['capa']['name'])) {
            $diretorio = "uploads/livros/";
            if (!is_dir($diretorio)) mkdir($diretorio, 0755, true);

            $capa = basename($_FILES['capa']['name']);
            $caminho = $diretorio . $capa;

            if (!move_uploaded_file($_FILES['capa']['tmp_name'], $caminho)) {
                $msg = " Erro ao carregar capa do livro.";
            }
        }

        $sql = "UPDATE livros SET titulo=?, ano_publicacao=?, autor_id=?";
        if ($capa) $sql .= ", capa=?";
        $sql .= " WHERE id=?";

        $stmt = $conn->prepare($sql);

        if ($capa) {
            $stmt->bind_param("siisi", $titulo, $ano_publicacao, $autor_id, $capa, $id);
        } else {
            $stmt->bind_param("siii", $titulo, $ano_publicacao, $autor_id, $id);
        }

        if ($stmt->execute()) {
            $msg = " Livro atualizado com sucesso!";
        } else {
            $msg = " Erro ao atualizar livro: " . $conn->error;
        }
    }
}

$autores = $conn->query("SELECT * FROM autores ORDER BY nome ASC")->fetch_all(MYSQLI_ASSOC);
$livros = $conn->query("SELECT * FROM livros ORDER BY titulo ASC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Registos</title>
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
                <a href="ligar_autor_livro.php">Ligar Autor-Livro</a>
            </nav>
        </div>
    </header>

    <main class="container-lg my-5">
        <h2>Editar Autores</h2>

        <?php if ($msg): ?>
            <div class="alert alert-info"><?= $msg ?></div>
        <?php endif; ?>

        <?php foreach ($autores as $autor): ?>
            <form action="editar.php" method="POST" enctype="multipart/form-data" class="mb-4 border rounded p-3">
                <input type="hidden" name="id" value="<?= $autor['id'] ?>">
                <input type="text" name="nome" value="<?= htmlspecialchars($autor['nome']) ?>" required class="form-control mb-2" />
                <input type="date" name="data_nascimento" value="<?= $autor['data_nascimento'] ?>" class="form-control mb-2" />
                <input type="text" name="nacionalidade" value="<?= htmlspecialchars($autor['nacionalidade']) ?>" class="form-control mb-2" />
                <label for="foto" class="form-label">Foto:</label>
                <input type="file" name="foto" class="form-control mb-2" />
                <button type="submit" name="editar_autor" class="btn btn-warning">Atualizar Autor</button>
            </form>
        <?php endforeach; ?>

        <h2 class="mt-5">Editar Livros</h2>

        <?php foreach ($livros as $livro): ?>
            <form action="editar.php" method="POST" enctype="multipart/form-data" class="mb-4 border rounded p-3">
                <input type="hidden" name="id" value="<?= $livro['id'] ?>">
                <input type="text" name="titulo" value="<?= htmlspecialchars($livro['titulo']) ?>" required class="form-control mb-2" />
                <input type="number" name="ano_publicacao" value="<?= $livro['ano_publicacao'] ?>" class="form-control mb-2" />

                <label for="autor_id" class="form-label">Autor:</label>
                <select name="autor_id" required class="form-select mb-2">
                    <?php foreach ($autores as $autor): ?>
                        <option value="<?= $autor['id'] ?>" <?= $livro['autor_id'] == $autor['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($autor['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="capa" class="form-label">Capa:</label>
                <input type="file" name="capa" class="form-control mb-2" />
                <button type="submit" name="editar_livro" class="btn btn-warning">Atualizar Livro</button>
            </form>
        <?php endforeach; ?>
    </main>

    <footer class="container-fluid text-center bg-light py-3 border-top">
        <div class="container-lg">
            <p>&copy; <?= date("Y") ?> Biblioteca.</p>
        </div>
    </footer>
</body>
</html>