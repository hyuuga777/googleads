<?php
/**
 * Cyborg Update Script - Atualiza a pasta 777 via upload direto
 * Coloque este arquivo em public_html/ e acesse uma vez para instalar os arquivos.
 * REMOVA após usar por segurança.
 */

// Proteção básica
$token = isset($_GET['t']) ? $_GET['t'] : '';
if ($token !== 'cyborg2026upd') {
    die('Acesso negado.');
}

$targetDir = __DIR__ . '/777/';

// Arquivos a criar/atualizar - conteúdo dos arquivos críticos
$files = [];

// config.js
$files['config.js'] = <<<'JS'
// Configuração de API Cyborg Orchestrator
window.CYBORG_CONFIG = {
    api_url: "https://googleads.agenciacyborg.com/api"
};
JS;

echo "<pre>";
echo "Cyborg Update Script\n";
echo "====================\n\n";

foreach ($files as $name => $content) {
    $path = $targetDir . $name;
    if (file_put_contents($path, $content) !== false) {
        echo "✅ $name atualizado\n";
    } else {
        echo "❌ Erro ao escrever $name\n";
    }
}

echo "\nVerificando arquivos na pasta 777:\n";
$allFiles = scandir($targetDir);
foreach ($allFiles as $f) {
    if ($f === '.' || $f === '..') continue;
    $fp = $targetDir . $f;
    echo sprintf("  %s (%d bytes)\n", $f, filesize($fp));
}

echo "\nDone! Remova este arquivo após usar.";
echo "</pre>";
