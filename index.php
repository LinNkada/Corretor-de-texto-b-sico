<?php
include_once("conexao.php"); // conecta ao banco

$apiKey = "SUA_CHAVE_OPENAI_AQUI"; // substitua pela sua chave

$resposta = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $texto = $_POST["texto"];

    // Monta a requisição para a OpenAI
    $data = [
        "model" => "gpt-3.5-turbo",
        "messages" => [
            ["role" => "system", "content" => "Você é um corretor de texto que melhora ortografia e gramática."],
            ["role" => "user", "content" => $texto]
        ]
    ];

    // Envio via cURL
    $ch = curl_init("https://api.openai.com/v1/chat/completions");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "Authorization: Bearer $apiKey"
        ],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_SSL_VERIFYPEER => false // apenas para teste local
    ]);

    $res = curl_exec($ch);

    if (curl_errno($ch)) {
        $resposta = "Erro cURL: " . curl_error($ch);
    } else {
        $json = json_decode($res, true);
        $resposta = $json["choices"][0]["message"]["content"] ?? "Erro ao gerar resposta.";
    }

    curl_close($ch);

    // Salva no banco
    if (!empty($texto) && !empty($resposta)) {
        $stmt = $conn->prepare("INSERT INTO historico (texto_original, texto_corrigido) VALUES (?, ?)");
        $stmt->bind_param("ss", $texto, $resposta);
        $stmt->execute();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Corretor de Texto com IA</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Corretor de Texto com IA</h2>
    <form method="POST">
        <textarea name="texto" placeholder="Digite seu texto..." required><?= htmlspecialchars($_POST["texto"] ?? "") ?></textarea>
        <button type="submit">Corrigir</button>
    </form>

    <?php if (!empty($resposta)): ?>
    <div class="resultado">
        <h3>Texto Corrigido:</h3>
        <p><?= nl2br(htmlspecialchars($resposta)) ?></p>
    </div>
    <?php endif; ?>
</div>
</body>
</html>
