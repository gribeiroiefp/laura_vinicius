<?php
$conn = mysqli_connect('127.0.0.1', 'root', '', 'livros_db');
if (!$conn) {
    die('Erro na ligação: ' . mysqli_connect_error());
}
 
$mensagem = "";
 
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = trim($_POST['nome']);
    $nacionalidade = trim($_POST['nacionalidade']);
    $data_nascimento = $_POST['data_nascimento'];
 
    // Verifica se os campos obrigatórios foram preenchidos
    if (empty($nome) || empty($nacionalidade)) {
        $mensagem = "<div class='alert alert-danger'>Preencha todos os campos obrigatórios.</div>";
    } else {
        // Upload da foto
        $foto = null;
        if (!empty($_FILES['foto']['name'])) {
            $pasta = "uploads/fotos/";
            if (!is_dir($pasta)) {
                mkdir($pasta, 0777, true);
            }
            $foto_nome = time() . "_" . basename($_FILES['foto']['name']);
            $destino = $pasta . $foto_nome;
 
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
                $foto = $foto_nome;
            } else {
                $mensagem = "<div class='alert alert-danger'>Erro ao carregar a foto.</div>";
            }
        }
 
        // Inserir autor
        $stmt = $conn->prepare("INSERT INTO autores (nome, data_nascimento, nacionalidade, foto) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nome, $data_nascimento, $nacionalidade, $foto);
 
        if ($stmt->execute()) {
            $mensagem = "<div class='alert alert-success'>Autor inserido com sucesso!</div>";
        } else {
            $mensagem = "<div class='alert alert-danger'>Erro: " . $stmt->error . "</div>";
        }
 
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>Inserir Autor</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<header class="container-fluid bg-light py-3 mb-4 shadow-sm">
<div class="container-lg d-flex justify-content-between align-items-center">
<h1> Inserir Autor</h1>
<nav>
<a href="index.php" class="me-3">Página inicial</a>
<a href="inserir_livro.php" class="me-3">Inserir Livro</a>
<a href="editar.php" class="me-3">Editar</a>
<a href="ligar_autor_livro.php">Ligar Autor-Livro</a>
</nav>
</div>
</header>
 
<div class="container-lg">
<?= $mensagem ?>
<form action="" method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm">
<div class="mb-3">
<label for="nome" class="form-label">Nome *</label>
<input type="text" name="nome" id="nome" class="form-control" required>
</div>
<div class="mb-3">
<label for="data_nascimento" class="form-label">Data de Nascimento</label>
<input type="date" name="data_nascimento" id="data_nascimento" class="form-control">
</div>
<div class="mb-3">
<label for="nacionalidade" class="form-label">Nacionalidade *</label>
<input type="text" name="nacionalidade" id="nacionalidade" class="form-control" required>
</div>
<div class="mb-3">
<label for="foto" class="form-label">Foto</label>
<input type="file" name="foto" id="foto" class="form-control">
</div>
<button type="submit" class="btn btn-success">Inserir Autor</button>
</form>
</div>
 
<footer class="container-fluid text-center py-3 mt-5 bg-light border-top">
<div class="container-lg">
<p>&copy; <?= date("Y") ?> Autores & Livros.</p>
</div>
</footer>
</body>
</html>
 
<?php mysqli_close($conn); ?>