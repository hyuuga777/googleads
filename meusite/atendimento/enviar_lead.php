<?php
// Configurações de email da Hostinger
$email_user = 'perguntas@agenciacyborg.com';
$email_password = '?88?^zzmtY';
$email_destinatario = 'brunosantos@agenciacyborg.com.br';

// Headers CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Responder OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Função para enviar email
function enviarEmail($dados) {
    global $email_user, $email_destinatario;
    
    $assunto = "🚀 Novo Lead - " . $dados['nome'];
    $data = date('d/m/Y H:i:s');
    
    $mensagem = "
    NOVO LEAD CAPTURADO
    
    Nome: " . $dados['nome'] . "
    Telefone: " . $dados['telefone'] . "
    Email: " . $dados['email'] . "
    Como podemos ajudar: " . $dados['ajuda'] . "
    
    Data/Hora: $data
    
    --
    Agência Cyborg - Sistema de Captura de Leads
    ";
    
    $headers = "From: $email_user\r\n";
    $headers .= "Reply-To: " . $dados['email'] . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    return mail($email_destinatario, $assunto, $mensagem, $headers);
}

// Processar requisição POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Obter dados
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
    
    // Salvar em arquivo (backup)
    $log = date('Y-m-d H:i:s') . " | " . 
           $dados['nome'] . " | " . 
           $dados['telefone'] . " | " . 
           $dados['email'] . " | " . 
           substr($dados['ajuda'], 0, 50) . "...\n";
    
    file_put_contents('leads.txt', $log, FILE_APPEND | LOCK_EX);
    
    // Tentar enviar email
    $email_enviado = enviarEmail($dados);
    
    // Sempre retornar sucesso (dados foram salvos)
    echo json_encode([
        'success' => true,
        'message' => 'Lead capturado com sucesso!',
        'email_enviado' => $email_enviado
    ]);
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['test'])) {
    
    // Teste
    echo json_encode([
        'status' => 'ok',
        'message' => 'PHP funcionando!',
        'timestamp' => date('c'),
        'mail_function' => function_exists('mail') ? 'sim' : 'não'
    ]);
    
} else {
    
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    
}
?>

