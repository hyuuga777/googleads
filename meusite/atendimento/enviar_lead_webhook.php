<?php
// Configurações
$destinatario = 'brunosantos@agenciacyborg.com.br';
$webhook_url = 'https://formspree.io/f/xpwagqpb'; // Webhook gratuito para emails

// Headers CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Função para enviar via webhook
function enviarViaWebhook($dados) {
    global $webhook_url, $destinatario;
    
    // Preparar dados para o webhook
    $webhook_data = array(
        'email' => $destinatario,
        'subject' => '🚀 Novo Lead - ' . $dados['nome'],
        'message' => "
NOVO LEAD CAPTURADO

Nome: " . $dados['nome'] . "
Telefone: " . $dados['telefone'] . "
Email: " . $dados['email'] . "
Como podemos ajudar: " . $dados['ajuda'] . "

Data/Hora: " . date('d/m/Y H:i:s') . "

--
Agência Cyborg - Sistema de Captura de Leads
        ",
        '_replyto' => $dados['email']
    );
    
    // Enviar via cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $webhook_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($webhook_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/x-www-form-urlencoded',
        'Accept: application/json'
    ));
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $http_code == 200;
}

// Função para enviar email direto (método simples)
function enviarEmailDireto($dados) {
    global $destinatario;
    
    $assunto = "🚀 Novo Lead - " . $dados['nome'];
    $mensagem = "
NOVO LEAD CAPTURADO

Nome: " . $dados['nome'] . "
Telefone: " . $dados['telefone'] . "
Email: " . $dados['email'] . "
Como podemos ajudar: " . $dados['ajuda'] . "

Data/Hora: " . date('d/m/Y H:i:s') . "

--
Agência Cyborg - Sistema de Captura de Leads
    ";
    
    $headers = "From: noreply@" . $_SERVER['HTTP_HOST'] . "\r\n";
    $headers .= "Reply-To: " . $dados['email'] . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    return mail($destinatario, $assunto, $mensagem, $headers);
}

// Processar requisição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $input = file_get_contents('php://input');
    $dados = json_decode($input, true);
    
    if (!$dados) {
        $dados = $_POST;
    }
    
    // Validar dados
    if (empty($dados['nome']) || empty($dados['telefone']) || 
        empty($dados['email']) || empty($dados['ajuda'])) {
        
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
        exit;
    }
    
    // Salvar em arquivo (backup principal)
    $log = date('Y-m-d H:i:s') . " | " . 
           $dados['nome'] . " | " . 
           $dados['telefone'] . " | " . 
           $dados['email'] . " | " . 
           substr($dados['ajuda'], 0, 50) . "...\n";
    
    file_put_contents('leads.txt', $log, FILE_APPEND | LOCK_EX);
    
    // Salvar dados completos
    $dados_completos = array(
        'id' => uniqid(),
        'timestamp' => date('c'),
        'nome' => $dados['nome'],
        'telefone' => $dados['telefone'],
        'email' => $dados['email'],
        'ajuda' => $dados['ajuda'],
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    );
    
    file_put_contents('leads_completos.json', 
        json_encode($dados_completos, JSON_PRETTY_PRINT) . ",\n", 
        FILE_APPEND | LOCK_EX);
    
    // Tentar múltiplos métodos de envio
    $email_enviado = false;
    $metodo_usado = '';
    
    // Método 1: Email direto
    if (enviarEmailDireto($dados)) {
        $email_enviado = true;
        $metodo_usado = 'email_direto';
    }
    
    // Método 2: Webhook (se email direto falhar)
    if (!$email_enviado && function_exists('curl_init')) {
        if (enviarViaWebhook($dados)) {
            $email_enviado = true;
            $metodo_usado = 'webhook';
        }
    }
    
    // Salvar tentativa de email
    $email_log = date('Y-m-d H:i:s') . " | " . 
                ($email_enviado ? 'SUCESSO' : 'FALHOU') . " | " . 
                $metodo_usado . " | " . 
                $dados['nome'] . " | " . 
                $dados['email'] . "\n";
    
    file_put_contents('emails_log.txt', $email_log, FILE_APPEND | LOCK_EX);
    
    // Sempre retornar sucesso (dados foram salvos)
    echo json_encode([
        'success' => true,
        'message' => 'Lead capturado com sucesso!',
        'email_enviado' => $email_enviado,
        'metodo' => $metodo_usado,
        'timestamp' => date('c')
    ]);
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['test'])) {
    
    echo json_encode([
        'status' => 'ok',
        'message' => 'Sistema funcionando!',
        'timestamp' => date('c'),
        'funcoes' => [
            'mail' => function_exists('mail') ? 'sim' : 'não',
            'curl' => function_exists('curl_init') ? 'sim' : 'não'
        ],
        'servidor' => $_SERVER['HTTP_HOST'] ?? 'unknown',
        'php_version' => phpversion()
    ]);
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['leads'])) {
    
    echo "<h2>📊 Leads Capturados</h2>";
    
    if (file_exists('leads.txt')) {
        echo "<h3>Resumo:</h3>";
        echo "<pre>" . htmlspecialchars(file_get_contents('leads.txt')) . "</pre>";
    }
    
    if (file_exists('emails_log.txt')) {
        echo "<h3>Log de Emails:</h3>";
        echo "<pre>" . htmlspecialchars(file_get_contents('emails_log.txt')) . "</pre>";
    }
    
    if (!file_exists('leads.txt')) {
        echo "<p>Nenhum lead capturado ainda.</p>";
    }
    
} else {
    
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    
}
?>

