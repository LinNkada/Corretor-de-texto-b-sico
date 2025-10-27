<?php
// Dados de conexão com MySQL
$servidor = "localhost";
$usuario  = "root";
$senha    = "";
$banco    = "corretor_db";

// Cria a conexão
$conn = new mysqli($servidor, $usuario, $senha, $banco);

// Verifica se ocorreu erro
if ($conn->connect_error) {
    die("Erro na conexão com o banco: " . $conn->connect_error);
}
?>
