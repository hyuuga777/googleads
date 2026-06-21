<?php
require_once __DIR__ . '/auth_check.php';

if (!$authenticated) {
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CYBORG ORCHESTRATOR — Autenticação</title>
    <link rel="stylesheet" href="googleads_dashboard.css">
</head>
<body>
    <div class="ambient-glow-1"></div>
    <div class="ambient-glow-2"></div>
    <div class="login-wrapper" style="display: flex;">
        <div class="login-card">
            <div class="login-logo">
                <h1>CYBORG ORCHESTRATOR</h1>
                <p>AI Google Ads Engine Controller</p>
            </div>
            
            <div class="login-terminal">
                [SYSTEM] AGENT ORCHESTRATOR STATUS: ONLINE<br>
                [SYSTEM] INICIALIZANDO PROTOCOLO SECURE_ACCESS_V3...<br>
                [SYSTEM] DIGITE A CHAVE DO ORQUESTRADOR PARA LIBERAR MODIFICAÇÕES.
            </div>

            <?php if (!empty($error)): ?>
                <div class="error-box" style="display: block;">[!] <?php echo $error; ?></div>
            <?php endif; ?>

            <form action="<?php echo $_SERVER['PHP_SELF'] . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''); ?>" method="POST">
                <input type="hidden" name="action" value="login">
                <div class="input-group">
                    <label for="password">Chave de Orquestração</label>
                    <input type="password" id="password" name="password" class="holo-input" placeholder="••••••••" required autocomplete="off">
                </div>
                <button type="submit" class="cyber-btn">Autenticar Operador</button>
            </form>
        </div>
    </div>
</body>
</html>
<?php
} else {
    include __DIR__ . '/strategies.html';
}
?>