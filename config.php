<?php
// Conexão com MySQL
$servername = "localhost";
$username = "root"; // Altere conforme seu setup
$password = "";
$dbname = "resumos_ia";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}
?>