<?php
// Ligação à base de dados
$conn = mysqli_connect('127.0.0.1', 'root', '', 'livros_db');
if (!$conn) {
    die('Erro na ligação: ' . mysqli_connect_error());
}

$msg = "";

// Obter lista de autores e livros
$autores = mysqli_query($conn, "SELECT * FROM autores ORDER BY nome ASC");
$livros = mysqli_query($conn, "SELECT * FROM livros ORDER BY titulo ASC");

// Edição de Autor
if (isset($_POST['editar_autor'])) {
    $autor_id = $_POST['autor_id'];
    $nome = trim($_POST['nome']);
    $bio = trim($_POST['bio']);

    if ($nome == "") {
        $msg = "⚠️ O nome do autor é obrigatório!";
    } else {
        // Upload da foto do autor
        $foto_nome = null;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $dir = "upload/fotos/";
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            $foto_nome = time() . "_" . basename($_FILES['foto']['name']);
            $caminho = $dir . $foto_nome;

            if (!move_uploaded_file($_FILES['foto']['tmp_name'], $caminho)) {
                $msg = "Erro ao enviar a foto.";
                $foto_nome = null;
            }
        }

        if ($foto_nome) {
            $sql = "UPDATE autores SET nome=?, bio=?, foto=? WHERE id=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssi", $nome, $bio, $foto_nome, $autor_id);
        } else {
            $sql = "UPDATE autores SET nome=?, bio=? WHERE id=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssi", $nome, $bio, $autor_id);
        }

        if (mysqli_stmt_execute($stmt)) {
            $msg = " Autor atualizado com sucesso!";
        } else {
            $msg = " Erro ao atualizar autor: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}

// Edição de Livro
if (isset($_POST['editar_livro'])) {
    $livro_id = $_POST['livro_id'];
    $titulo = trim($_POST['titulo']);
    $ano = $_POST['ano'];

    if ($titulo == "" || $ano == "") {
        $msg = " Título e ano são obrigatórios!";
    } else {
        // Upload da capa do livro
        $capa_nome = null;
        if (isset($_FILES['capa']) && $_FILES['capa']['error'] == 0) {
            $dir = "upload/capas/";
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            $capa_nome = time() . "_" . basename($_FILES['capa']['name']);
            $caminho = $dir . $capa_nome;

            if (!move_uploaded_file($_FILES['capa']['tmp_name'], $caminho)) {
                $msg = "Erro ao enviar a capa.";
                $capa_nome = null;
            }
        }

        if ($capa_nome) {
            $sql = "UPDATE livros SET titulo=?, ano=?, capa=? WHERE id=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sisi", $titulo, $ano, $capa_nome, $livro_id);
        } else {
            $sql = "UPDATE livros SET titulo=?, ano=? WHERE id=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sii", $titulo, $ano, $livro_id);
        }

        if (mysqli_stmt_execute($stmt)) {
            $msg = " Livro atualizado com sucesso!";
        } else {
            $msg = " Erro ao atualizar livro: " . mysqli_error($conn);
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
    <title>Editar - Autores & Livros</title>
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
            <a href="editar.php" class="me-3 text-warning">Editar</a>
            <a href="ligar_autor_livro.php" class="me-3 text-white">Ligar Autor-Livro</a>
            <a href="apagar.php" class="me-3 text-white">Apagar</a>
        </nav>
    </div>
</header>

<div class="container-lg">
    <h2 class="mb-4"> Editar Autores e Livros</h2>

    <?php if ($msg): ?>
        <div class="alert alert-info"><?= $msg ?></div>
    <?php endif; ?>

    <!-- Editar Autor -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Editar Autor</div>
        <div class="card-body">
            <form action="editar.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="autor_id" class="form-label">Selecione o autor</label>
                    <select name="autor_id" id="autor_id" class="form-select" required>
                        <option value="">-- Selecione --</option>
                        <?php mysqli_data_seek($autores, 0); ?>
                        <?php while ($autor = mysqli_fetch_assoc($autores)): ?>
                            <option value="<?= $autor['id'] ?>"><?= htmlspecialchars($autor['nome']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="nome" class="form-label">Novo Nome *</label>
                    <input type="text" name="nome" id="nome" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="bio" class="form-label">Nova Biografia</label>
                    <textarea name="bio" id="bio" class="form-control"></textarea>
                </div>
                <div class="mb-3">
                    <label for="foto" class="form-label">Nova Foto</label>
                    <input type="file" name="foto" id="foto" class="form-control" accept="image/*">
                </div>
                <button type="submit" name="editar_autor" class="btn btn-primary">Salvar Alterações</button>
            </form>
        </div>
    </div>

    <!-- Editar Livro -->
    <div class="card">
        <div class="card-header bg-success text-white">Editar Livro</div>
        <div class="card-body">
            <form action="editar.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="livro_id" class="form-label">Selecione o livro</label>
                    <select name="livro_id" id="livro_id" class="form-select" required>
                        <option value="">-- Selecione --</option>
                        <?php mysqli_data_seek($livros, 0); ?>
                        <?php while ($livro = mysqli_fetch_assoc($livros)): ?>
                            <option value="<?= $livro['id'] ?>"><?= htmlspecialchars($livro['titulo']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="titulo" class="form-label">Novo Título *</label>
                    <input type="text" name="titulo" id="titulo" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="ano" class="form-label">Novo Ano *</label>
                    <input type="number" name="ano" id="ano" class="form-control" min="1000" max="2099" required>
                </div>
                <div class="mb-3">
                    <label for="capa" class="form-label">Nova Capa</label>
                    <input type="file" name="capa" id="capa" class="form-control" accept="image/*">
                </div>
                <button type="submit" name="editar_livro" class="btn btn-success">Salvar Alterações</button>
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
