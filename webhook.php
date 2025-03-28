<?php
// Token secreto para segurança
$secret = "@Theus12_";  

// Calculando a assinatura com base no conteúdo do webhook
$signature = "sha256=" . hash_hmac("sha256", file_get_contents("php://input"), $secret);

// Verificando a assinatura para garantir que a requisição seja do GitHub
if ($_SERVER["HTTP_X_HUB_SIGNATURE_256"] !== $signature) {
    http_response_code(403);
    die("Acesso negado.");
}

// Rodando o deploy no servidor
shell_exec("cd /var/www/smartonec3 && git pull origin main && docker-compose up -d");
http_response_code(200);
