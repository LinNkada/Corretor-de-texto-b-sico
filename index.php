<?php
include 'config.php';

$resumo = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $texto = $_POST['texto'];

    // Requisição HTTP para Hugging Face Inference API (modelo de resumo)
    $url = "https://api-inference.huggingface.co/models/facebook/bart-large-cnn";
    $data = json_encode(["inputs" => $texto, "parameters" => ["max_length" => 100, "min_length" => 30]]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    $resumo = $result[0]['summary_text'] ?? "Erro ao gerar resumo.";

    // Salvar no banco de dados (ponto adicional)
    $stmt = $conn->prepare("INSERT INTO historico (texto_original, resumo) VALUES (?, ?)");
    $stmt->bind_param("ss", $texto, $resumo);
    $stmt->execute();
    $stmt->close();
}

// Buscar histórico (opcional, para exibir)
$historico = $conn->query("SELECT * FROM historico ORDER BY id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Gerador de Resumos com IA</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>Gerador de Resumos Acadêmicos</h1>
    <form method="POST">
        <textarea name="texto" placeholder="Cole seu texto acadêmico aqui..." required></textarea><br>
        <button type="submit">Gerar Resumo</button>
    </form>
    <?php if ($resumo): ?>
        <h2>Resumo Gerado:</h2>
        <p><?php echo htmlspecialchars($resumo); ?></p>
    <?php endif; ?>

    <h2>Histórico Recente</h2>
    <ul>
        <?php while ($row = $historico->fetch_assoc()): ?>
            <li><strong>Texto:</strong> <?php echo substr(htmlspecialchars($row['texto_original']), 0, 50); ?>...<br>
                <strong>Resumo:</strong> <?php echo htmlspecialchars($row['resumo']); ?>
            </li>
        <?php endwhile; ?>
    </ul>
</body>

</html>