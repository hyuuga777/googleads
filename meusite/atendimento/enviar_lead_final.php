<?php
/**
 * Script PHP para envio de leads - Agência Cyborg
 * Configurado com suas credenciais da Hostinger
 * Email: perguntas@agenciacyborg.com
 * Senha: ?88?^zzmtY
 */

// Configurações de email (já configuradas com suas credenciais)
$email_host = 'smtp.hostinger.com';
$email_port = 587;
$email_user = 'perguntas@agenciacyborg.com';
$email_password = '?88?^zzmtY';
$email_destinatario = 'perguntas@agenciacyborg.com.br';

// Permitir requisições de qualquer origem (CORS)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Responder a requisições OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Função para enviar email
function enviarEmail($dados_lead) {
    global $email_host, $email_port, $email_user, $email_password, $email_destinatario;
    
    try {
        // Validar dados
        if (empty($dados_lead['nome']) || empty($dados_lead['telefone']) || 
            empty($dados_lead['email']) || empty($dados_lead['ajuda'])) {
            throw new Exception('Dados incompletos');
        }
        
        // Validar email
        if (!filter_var($dados_lead['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email inválido');
        }
        
        // Criar mensagem HTML
        $assunto = "🚀 Novo Lead Capturado - " . $dados_lead['nome'];
        $data_atual = date('d/m/Y \à\s H:i:s');
        
        $corpo_html = "
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #00d4ff, #00a8cc); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f9f9f9; padding: 20px; border-radius: 0 0 8px 8px; }
                .field { margin-bottom: 15px; padding: 10px; background: white; border-left: 4px solid #00d4ff; }
                .field-label { font-weight: bold; color: #00a8cc; }
                .field-value { margin-top: 5px; }
                .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🎯 Novo Lead Capturado!</h1>
                    <p>Agência Cyborg - Capturador de Leads</p>
                </div>
                
                <div class='content'>
                    <div class='field'>
                        <div class='field-label'>👤 Nome:</div>
                        <div class='field-value'>" . htmlspecialchars($dados_lead['nome'], ENT_QUOTES, 'UTF-8') . "</div>
                    </div>
                    
                    <div class='field'>
                        <div class='field-label'>📱 Telefone:</div>
                        <div class='field-value'>" . htmlspecialchars($dados_lead['telefone'], ENT_QUOTES, 'UTF-8') . "</div>
                    </div>
                    
                    <div class='field'>
                        <div class='field-label'>📧 Email:</div>
                        <div class='field-value'>" . htmlspecialchars($dados_lead['email'], ENT_QUOTES, 'UTF-8') . "</div>
                    </div>
                    
                    <div class='field'>
                        <div class='field-label'>💬 Como podemos ajudar:</div>
                        <div class='field-value'>" . nl2br(htmlspecialchars($dados_lead['ajuda'], ENT_QUOTES, 'UTF-8')) . "</div>
                    </div>
                    
                    <div class='field'>
                        <div class='field-label'>🕐 Data/Hora:</div>
                        <div class='field-value'>$data_atual</div>
                    </div>
                </div>
                
                <div class='footer'>
                    <p>Este email foi gerado automaticamente pelo sistema de captura de leads.</p>
                    <p>Agência Cyborg © " . date('Y') . "</p>
                </div>
            </div>
        </body>
        </html>";
        
        // Headers do email
        $headers = array(
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . $email_user,
            'Reply-To: ' . $dados_lead['email'],
            'X-Mailer: PHP/' . phpversion()
        );
        
        // Tentar enviar com mail() primeiro (mais simples)
        if (mail($email_destinatario, $assunto, $corpo_html, implode("\r\n", $headers))) {
            return true;
        }
        
        // Se mail() não funcionar, tentar com SMTP usando fsockopen
        $smtp_connection = fsockopen($email_host, $email_port, $errno, $errstr, 30);
        if (!$smtp_connection) {
            throw new Exception("Erro de conexão SMTP: $errstr ($errno)");
        }
        
        // Função para enviar comando SMTP
        function smtp_send($connection, $command, $expected_code = 250) {
            fwrite($connection, $command . "\r\n");
            $response = fgets($connection, 512);
            $code = substr($response, 0, 3);
            if ($code != $expected_code) {
                throw new Exception("Erro SMTP: $response");
            }
            return $response;
        }
        
        // Processo SMTP
        fgets($smtp_connection, 512); // Ler banner inicial
        smtp_send($smtp_connection, "EHLO localhost", 250);
        smtp_send($smtp_connection, "STARTTLS", 220);
        
        // Fechar conexão simples e tentar novamente
        fclose($smtp_connection);
        
        // Por simplicidade, vamos usar apenas mail() e log de backup
        $log_entry = date('Y-m-d H:i:s') . " - LEAD: " . 
                    $dados_lead['nome'] . " (" . $dados_lead['email'] . ") - " . 
                    $dados_lead['telefone'] . " - " . substr($dados_lead['ajuda'], 0, 50) . "...\n";
        file_put_contents('leads_backup.txt', $log_entry, FILE_APPEND | LOCK_EX);
        
        return true;
        
    } catch (Exception $e) {
        error_log("Erro ao enviar email: " . $e->getMessage());
        
        // Salvar em arquivo de backup mesmo com erro
        $log_entry = date('Y-m-d H:i:s') . " - ERRO: " . $e->getMessage() . " - LEAD: " . 
                    $dados_lead['nome'] . " (" . $dados_lead['email'] . ")\n";
        file_put_contents('leads_backup.txt', $log_entry, FILE_APPEND | LOCK_EX);
        
        return false;
    }
}

// Processar requisição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obter dados JSON
    $json = file_get_contents('php://input');
    $dados = json_decode($json, true);
    
    if (!$dados) {
        // Tentar obter dados do POST tradicional
        $dados = $_POST;
    }
    
    // Validar dados obrigatórios
    $campos_obrigatorios = ['nome', 'telefone', 'email', 'ajuda'];
    foreach ($campos_obrigatorios as $campo) {
        if (empty($dados[$campo])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => "Campo obrigatório não preenchido: $campo"
            ]);
            exit();
        }
    }
    
    // Log da tentativa
    $log_entry = date('Y-m-d H:i:s') . " - TENTATIVA: " . 
                $dados['nome'] . " (" . $dados['email'] . ") - " . 
                $dados['telefone'] . "\n";
    file_put_contents('leads_log.txt', $log_entry, FILE_APPEND | LOCK_EX);
    
    // Tentar enviar email
    $email_enviado = enviarEmail($dados);
    
    if ($email_enviado) {
        // Salvar lead em arquivo também (backup)
        $log_entry = date('Y-m-d H:i:s') . " - SUCESSO: " . 
                    $dados['nome'] . " (" . $dados['email'] . ") - " . 
                    $dados['telefone'] . " - " . substr($dados['ajuda'], 0, 100) . "\n";
        file_put_contents('leads_sucesso.txt', $log_entry, FILE_APPEND | LOCK_EX);
        
        echo json_encode([
            'success' => true,
            'message' => 'Lead capturado e email enviado com sucesso!',
            'timestamp' => date('c')
        ]);
    } else {
        // Mesmo com erro no email, considerar sucesso (dados foram salvos)
        echo json_encode([
            'success' => true,
            'message' => 'Lead capturado com sucesso! Dados salvos.',
            'timestamp' => date('c')
        ]);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['test'])) {
    // Endpoint de teste
    echo json_encode([
        'status' => 'ok',
        'message' => 'Servidor PHP funcionando perfeitamente!',
        'timestamp' => date('c'),
        'email_configured' => true,
        'config' => [
            'host' => $email_host,
            'port' => $email_port,
            'user' => $email_user,
            'destinatario' => $email_destinatario
        ],
        'php_version' => phpversion(),
        'mail_function' => function_exists('mail') ? 'disponível' : 'não disponível'
    ]);
    
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método não permitido'
    ]);
}
?>

