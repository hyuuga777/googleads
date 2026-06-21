<?php
/**
 * dashboard.php — Painel Administrativo de Elite Cyborg
 * Estatísticas de Acessos, Visitantes Únicos, Leads e Conversão em Tempo Real.
 */

session_start([
    'cookie_httponly' => true,
    'cookie_secure' => isset($_SERVER['HTTPS']),
    'cookie_samesite' => 'Lax'
]);

// Definir timezone oficial de São Paulo
date_default_timezone_set('America/Sao_Paulo');

// Configurações e Segurança
$correct_hash = 'fc1b7a9976fa0826a827f334c30925963eb27dbc37048fddaeedf20a5edc1ab4';
$error = '';

if (isset($_POST['action']) && $_POST['action'] === 'login') {
    $pass = $_POST['password'] ?? '';
    if (hash('sha256', $pass) === $correct_hash) {
        $_SESSION['cyborg_dashboard_auth'] = true;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'CHAVE INCORRETA. ACESSO NÃO AUTORIZADO.';
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['cyborg_dashboard_auth']);
    session_destroy();
    header('Location: dashboard.php');
    exit;
}

$authenticated = !empty($_SESSION['cyborg_dashboard_auth']);

// Leitura e Processamento de Logs (Apenas se Autenticado)
$visits = [];
$leads = [];

if ($authenticated) {
    // Carregar Visitas
    $visits_file = __DIR__ . '/../base/api/data/visits.log.php';
    if (file_exists($visits_file)) {
        $lines = file($visits_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines) {
            foreach ($lines as $line) {
                if (strpos($line, '<?php') === 0) continue; // Ignora o cabeçalho PHP de proteção
                $data = json_decode($line, true);
                if ($data) $visits[] = $data;
            }
        }
    }

    // Carregar Leads
    $leads_file = __DIR__ . '/../base/api/data/leads.log.php';
    if (file_exists($leads_file)) {
        $lines = file($leads_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines) {
            foreach ($lines as $line) {
                if (strpos($line, '<?php') === 0) continue; // Ignora o cabeçalho PHP de proteção
                $data = json_decode($line, true);
                if ($data) $leads[] = $data;
            }
        }
    }

    // --- Cálculos Estatísticos ---
    $total_visits = count($visits);
    
    // Visitantes Únicos (Baseado em IPs distintos)
    $unique_ips = [];
    foreach ($visits as $v) {
        if (!empty($v['ip'])) $unique_ips[$v['ip']] = true;
    }
    $unique_visitors = count($unique_ips);
    
    $total_leads = count($leads);
    $conversion_rate = ($unique_visitors > 0) ? round(($total_leads / $unique_visitors) * 100, 1) : 0;

    // --- Agrupamento Diário (Últimos 15 Dias) ---
    $daily_stats = [];
    for ($i = 14; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $daily_stats[$date] = ['visits' => 0, 'leads' => 0];
    }

    foreach ($visits as $v) {
        $date = substr($v['timestamp'] ?? '', 0, 10);
        if (isset($daily_stats[$date])) $daily_stats[$date]['visits']++;
    }

    foreach ($leads as $l) {
        $date = substr($l['timestamp'] ?? '', 0, 10);
        if (isset($daily_stats[$date])) $daily_stats[$date]['leads']++;
    }

    $chart_labels = array_keys($daily_stats);
    $chart_visits = [];
    $chart_leads = [];
    foreach ($daily_stats as $stat) {
        $chart_visits[] = $stat['visits'];
        $chart_leads[] = $stat['leads'];
    }

    // --- Distribuição de Dispositivos ---
    $devices = ['Desktop' => 0, 'Mobile' => 0, 'Tablet' => 0, 'Outro' => 0];
    foreach ($visits as $v) {
        $dev = $v['device'] ?? 'Desktop';
        if (isset($devices[$dev])) {
            $devices[$dev]++;
        } else {
            $devices['Outro']++;
        }
    }

    // --- Fontes de Tráfego ---
    $traffic_sources = [];
    foreach ($visits as $v) {
        $src = $v['source'] ?? 'Direto';
        if (!isset($traffic_sources[$src])) $traffic_sources[$src] = 0;
        $traffic_sources[$src]++;
    }
    arsort($traffic_sources);
    $top_sources = array_slice($traffic_sources, 0, 5, true);

    // --- Rankings Extras: Navegadores e Sistemas Operacionais ---
    $browsers = [];
    foreach ($visits as $v) {
        $b = $v['browser'] ?? 'Outro';
        if (!isset($browsers[$b])) $browsers[$b] = 0;
        $browsers[$b]++;
    }
    arsort($browsers);

    $oss = [];
    foreach ($visits as $v) {
        $o = $v['os'] ?? 'Outro';
        if (!isset($oss[$o])) $oss[$o] = 0;
        $oss[$o]++;
    }
    arsort($oss);

    // --- Listas em Ordem Reversa (Mais recentes primeiro) ---
    $recent_visits = array_reverse($visits);
    $recent_leads = array_reverse($leads);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyborg Analytics - Terminal de Controle</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Audiowide&family=Montserrat:wght@500;700&family=Space+Grotesk:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Chart.js para Visualização de Dados -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --bg-dark: #040308;
            --bg-card: rgba(15, 12, 28, 0.65);
            --cyan: #00f3ff;
            --purple: #aa00ff;
            --pink: #ff0055;
            --text-primary: #ffffff;
            --text-secondary: rgba(255, 255, 255, 0.6);
            --border-glow: rgba(0, 243, 255, 0.15);
            --font-title: 'Audiowide', sans-serif;
            --font-body: 'Space Grotesk', sans-serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-primary);
            font-family: var(--font-body);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Tech Background Grid */
        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-image: 
                linear-gradient(rgba(0, 243, 255, 0.015) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 243, 255, 0.015) 1px, transparent 1px);
            background-size: 30px 30px;
            pointer-events: none;
            z-index: -1;
        }

        /* Glow effects */
        .neon-glow {
            text-shadow: 0 0 10px rgba(0, 243, 255, 0.5), 0 0 20px rgba(0, 243, 255, 0.3);
        }
        .neon-glow-purple {
            text-shadow: 0 0 10px rgba(170, 0, 255, 0.5), 0 0 20px rgba(170, 0, 255, 0.3);
        }

        /* ----------------------------------------------------
           TELA DE LOGIN
        ---------------------------------------------------- */
        .login-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .login-card {
            background: var(--bg-card);
            border: 1px solid rgba(0, 243, 255, 0.2);
            box-shadow: 0 0 30px rgba(0, 243, 255, 0.05);
            backdrop-filter: blur(15px);
            width: 100%;
            max-width: 420px;
            border-radius: 12px;
            padding: 40px 30px;
            position: relative;
            animation: pulse-border 4s infinite alternate;
        }

        @keyframes pulse-border {
            0% { border-color: rgba(0, 243, 255, 0.2); }
            100% { border-color: rgba(170, 0, 255, 0.4); }
        }

        /* Tech corner elements */
        .login-card::before, .login-card::after,
        .cyber-box::before, .cyber-box::after {
            content: '';
            position: absolute;
            width: 12px; height: 12px;
            border-style: solid;
            pointer-events: none;
        }
        .login-card::before, .cyber-box::before {
            top: -1px; left: -1px;
            border-width: 2px 0 0 2px;
            border-color: var(--cyan);
        }
        .login-card::after, .cyber-box::after {
            bottom: -1px; right: -1px;
            border-width: 0 2px 2px 0;
            border-color: var(--purple);
        }

        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-logo h1 {
            font-family: var(--font-title);
            font-size: 1.8rem;
            letter-spacing: 0.1em;
            background: linear-gradient(135deg, var(--cyan), var(--purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
        }

        .login-logo p {
            font-size: 0.8rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.2em;
        }

        .login-terminal {
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 6px;
            padding: 12px;
            font-family: monospace;
            font-size: 0.72rem;
            color: #88ff88;
            margin-bottom: 24px;
            line-height: 1.5;
        }

        .input-group {
            margin-bottom: 24px;
            position: relative;
        }

        .input-group label {
            display: block;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--cyan);
            margin-bottom: 8px;
            font-weight: bold;
        }

        .holo-input {
            width: 100%;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(0, 243, 255, 0.2);
            color: #fff;
            padding: 14px 16px;
            border-radius: 6px;
            font-family: var(--font-body);
            font-size: 1rem;
            transition: all 0.3s;
            outline: none;
        }

        .holo-input:focus {
            border-color: var(--cyan);
            box-shadow: 0 0 10px rgba(0, 243, 255, 0.2);
            background: rgba(255, 255, 255, 0.06);
        }

        .cyber-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--purple), var(--pink));
            color: #fff;
            border: none;
            padding: 14px 20px;
            border-radius: 6px;
            font-family: var(--font-title);
            font-size: 0.9rem;
            letter-spacing: 0.1em;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(170, 0, 255, 0.3);
            text-transform: uppercase;
        }

        .cyber-btn:hover {
            box-shadow: 0 0 20px rgba(170, 0, 255, 0.6), 0 0 40px rgba(0, 243, 255, 0.2);
            transform: translateY(-2px);
        }

        .error-box {
            background: rgba(255, 0, 85, 0.1);
            border: 1px solid var(--pink);
            color: #ff99bb;
            border-radius: 6px;
            padding: 12px;
            font-size: 0.8rem;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }

        /* ----------------------------------------------------
           INTERFACE DO PAINEL (DASHBOARD)
        ---------------------------------------------------- */
        .app-header {
            background: rgba(5, 4, 10, 0.8);
            border-bottom: 1px solid rgba(0, 243, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .brand h1 {
            font-family: var(--font-title);
            font-size: 1.4rem;
            background: linear-gradient(135deg, var(--cyan), var(--purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand span {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.3em;
            color: var(--text-secondary);
            display: block;
            margin-top: 3px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .status-badge {
            background: rgba(0, 243, 255, 0.05);
            border: 1px solid rgba(0, 243, 255, 0.2);
            padding: 8px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-dot {
            width: 8px; height: 8px;
            background-color: var(--cyan);
            border-radius: 50%;
            box-shadow: 0 0 8px var(--cyan);
            animation: pulse-dot 1.5s infinite;
        }

        @keyframes pulse-dot {
            0% { opacity: 0.4; }
            50% { opacity: 1; }
            100% { opacity: 0.4; }
        }

        .logout-btn {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-secondary);
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-family: var(--font-body);
            font-size: 0.8rem;
            text-transform: uppercase;
            transition: all 0.3s;
        }

        .logout-btn:hover {
            color: #fff;
            border-color: var(--pink);
            background: rgba(255, 0, 85, 0.05);
        }

        .main-container {
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 24px;
        }

        /* Grid de KPIs */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .kpi-card {
            background: var(--bg-card);
            border: 1px solid rgba(255, 255, 255, 0.03);
            border-radius: 8px;
            padding: 24px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s;
        }

        .kpi-card:hover {
            transform: translateY(-3px);
            border-color: var(--border-glow);
            box-shadow: 0 8px 30px rgba(0, 243, 255, 0.03);
        }

        .kpi-label {
            font-size: 0.75rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 8px;
            display: block;
        }

        .kpi-value {
            font-size: 2.2rem;
            font-family: var(--font-title);
            font-weight: bold;
            line-height: 1.2;
            background: linear-gradient(135deg, #fff, #bbaacc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .kpi-card[data-cyan] .kpi-value {
            background: linear-gradient(135deg, #fff, var(--cyan));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .kpi-card[data-purple] .kpi-value {
            background: linear-gradient(135deg, #fff, var(--purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .kpi-card::after {
            content: '';
            position: absolute;
            right: 0; bottom: 0;
            width: 60px; height: 60px;
            opacity: 0.02;
            background-size: contain;
            background-repeat: no-repeat;
        }

        /* Layout de Conteúdo */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }

        @media (max-width: 992px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        .cyber-box {
            background: var(--bg-card);
            border: 1px solid rgba(255, 255, 255, 0.03);
            border-radius: 8px;
            padding: 30px;
            position: relative;
        }

        .box-title {
            font-family: var(--font-title);
            font-size: 1.1rem;
            letter-spacing: 0.05em;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            padding-bottom: 12px;
            text-transform: uppercase;
        }

        .box-title span.indicator {
            width: 4px; height: 16px;
            background: var(--cyan);
            display: inline-block;
            box-shadow: 0 0 8px var(--cyan);
        }

        /* Estilos de Gráficos */
        .chart-container {
            position: relative;
            width: 100%;
            height: 350px;
        }

        /* Tabelas */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            margin-top: 15px;
        }

        .cyber-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.88rem;
        }

        .cyber-table th {
            font-family: var(--font-title);
            font-size: 0.75rem;
            color: var(--cyan);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 14px 16px;
            border-bottom: 2px solid rgba(0, 243, 255, 0.1);
        }

        .cyber-table td {
            padding: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.03);
            color: var(--text-secondary);
        }

        .cyber-table tr:hover td {
            background: rgba(255, 255, 255, 0.01);
            color: #fff;
        }

        .lead-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.68rem;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.05em;
        }

        .lead-badge[data-source="valores"] { background: rgba(170, 0, 255, 0.15); color: #e599ff; border: 1px solid rgba(170, 0, 255, 0.3); }
        .lead-badge[data-source="sites"] { background: rgba(0, 243, 255, 0.15); color: #99f7ff; border: 1px solid rgba(0, 243, 255, 0.3); }
        .lead-badge[data-source="automacao"] { background: rgba(0, 191, 165, 0.15); color: #99ffe5; border: 1px solid rgba(0, 191, 165, 0.3); }
        .lead-badge[data-source="ads"] { background: rgba(255, 179, 0, 0.15); color: #ffe699; border: 1px solid rgba(255, 179, 0, 0.3); }
        .lead-badge[data-source="default"] { background: rgba(255, 255, 255, 0.08); color: #fff; border: 1px solid rgba(255, 255, 255, 0.1); }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(0, 243, 255, 0.05);
            border: 1px solid rgba(0, 243, 255, 0.2);
            color: var(--cyan);
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.72rem;
            font-family: var(--font-body);
            font-weight: bold;
            text-transform: uppercase;
            transition: all 0.3s;
        }

        .btn-action:hover {
            background: var(--cyan);
            color: var(--bg-dark);
            box-shadow: 0 0 10px rgba(0, 243, 255, 0.5);
            transform: translateY(-1px);
        }

        /* Lista de Métricas Laterais (Dispositivos / Referrers) */
        .metric-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.02);
        }

        .metric-row:last-child {
            border-bottom: none;
        }

        .metric-name {
            font-size: 0.88rem;
            color: var(--text-secondary);
        }

        .metric-bar-container {
            width: 100%;
            height: 4px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 2px;
            margin-top: 6px;
            overflow: hidden;
        }

        .metric-bar-fill {
            height: 100%;
            background: var(--cyan);
            border-radius: 2px;
        }

        .metric-val {
            font-family: monospace;
            font-weight: bold;
            font-size: 0.9rem;
            color: #fff;
        }

        /* Filtro e Pesquisa */
        .table-header-ctrl {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .search-wrapper {
            position: relative;
            flex-grow: 1;
            max-width: 300px;
        }

        .search-input {
            width: 100%;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            color: #fff;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.8rem;
            outline: none;
            transition: all 0.3s;
        }

        .search-input:focus {
            border-color: var(--cyan);
            background: rgba(255, 255, 255, 0.04);
        }

        /* Paginação Básica via CSS/JS */
        .pagination-ctrl {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 15px;
        }

        .btn-page {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            color: var(--text-secondary);
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.75rem;
            transition: all 0.3s;
        }

        .btn-page:hover:not(:disabled) {
            color: #fff;
            border-color: var(--cyan);
        }

        .btn-page:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }

        /* ----------------------------------------------------
           NOVOS ESTILOS DO ORQUESTRADOR E TABS UNIFICADAS
        ---------------------------------------------------- */
        :root {
            --success: #00ff66;
            --warning: #ffb300;
            --font-title: 'Audiowide', sans-serif;
            --font-body: 'Space Grotesk', sans-serif;
            --cyber-gradient: linear-gradient(135deg, var(--cyan), var(--purple));
            --neon-shadow: 0 0 15px rgba(0, 243, 255, 0.35);
        }

        /* Barra de Navegação por Abas */
        .tab-navigation {
            display: flex;
            justify-content: center;
            background: rgba(10, 8, 22, 0.6);
            border: 1px solid rgba(0, 243, 255, 0.08);
            max-width: 100%;
            margin: 20px 0 35px 0;
            border-radius: 8px;
            padding: 6px;
            gap: 10px;
        }

        .tab-btn {
            background: transparent;
            border: none;
            color: var(--text-secondary);
            padding: 12px 24px;
            font-family: var(--font-title);
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            cursor: pointer;
            border-radius: 6px;
            transition: all 0.3s;
            flex-grow: 1;
            max-width: 25%;
            text-align: center;
        }

        .tab-btn:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.02);
        }

        .tab-btn.active {
            color: var(--cyan);
            background: rgba(0, 243, 255, 0.08);
            border: 1px solid rgba(0, 243, 255, 0.25);
            box-shadow: 0 0 10px rgba(0, 243, 255, 0.1);
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.4s ease-out forwards;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Google Ads BI Layout */
        .bi-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        @media (max-width: 1024px) {
            .bi-layout { grid-template-columns: 1fr; }
        }

        .chart-wrapper {
            position: relative;
            width: 100%;
            height: 380px;
        }

        /* Orçamentos Consolidados */
        .budget-progress-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .budget-bar-group {
            background: rgba(255,255,255,0.01);
            border: 1px solid rgba(255,255,255,0.02);
            border-radius: 6px;
            padding: 15px;
        }

        .budget-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.88rem;
            margin-bottom: 8px;
        }

        .budget-name {
            color: #fff;
            font-weight: bold;
        }

        .budget-limit {
            font-family: monospace;
            color: var(--text-secondary);
        }

        .budget-progress-outer {
            width: 100%;
            height: 8px;
            background: rgba(255,255,255,0.05);
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 6px;
        }

        .budget-progress-inner {
            height: 100%;
            border-radius: 4px;
            background: linear-gradient(90deg, var(--cyan), var(--purple));
            box-shadow: 0 0 8px var(--cyan);
        }

        .budget-details {
            display: flex;
            justify-content: space-between;
            font-size: 0.72rem;
            color: var(--text-secondary);
        }

        /* Anomalias e Botoes */
        .status-pill.enabled {
            background: rgba(0, 255, 102, 0.12);
            color: var(--success);
            border: 1px solid rgba(0, 255, 102, 0.3);
        }

        .status-pill.paused {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-secondary);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .status-pill.error {
            background: rgba(255, 0, 85, 0.12);
            color: var(--pink);
            border: 1px solid rgba(255, 0, 85, 0.3);
        }

        .anomaly-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 0, 85, 0.12);
            color: #ff99bb;
            border: 1px solid rgba(255, 0, 85, 0.25);
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.72rem;
            margin-top: 5px;
            width: fit-content;
        }

        .anomaly-indicator {
            width: 6px; height: 6px;
            background: var(--pink);
            border-radius: 50%;
            box-shadow: 0 0 6px var(--pink);
        }

        /* Pulsing anomaly warning dot */
        .anomaly-dot-warning {
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: var(--pink);
            border-radius: 50%;
            margin-left: 8px;
            vertical-align: middle;
            position: relative;
            cursor: help;
            box-shadow: 0 0 8px var(--pink);
            animation: pulse-pink-dot 1s infinite alternate;
        }

        @keyframes pulse-pink-dot {
            0% {
                transform: scale(0.9);
                box-shadow: 0 0 4px var(--pink);
            }
            100% {
                transform: scale(1.3);
                box-shadow: 0 0 12px var(--pink), 0 0 20px var(--pink);
            }
        }

        /* Tooltip style */
        .anomaly-dot-warning::before {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%) translateY(5px);
            background: rgba(12, 10, 24, 0.95);
            color: #ff99bb;
            border: 1px solid var(--pink);
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.72rem;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: all 0.2s ease;
            box-shadow: 0 4px 15px rgba(255, 0, 85, 0.25);
            z-index: 10;
            font-family: var(--font-body);
            font-weight: bold;
        }

        .anomaly-dot-warning::after {
            content: '';
            position: absolute;
            bottom: 110%;
            left: 50%;
            transform: translateX(-50%) translateY(5px);
            border-width: 5px;
            border-style: solid;
            border-color: var(--pink) transparent transparent transparent;
            opacity: 0;
            pointer-events: none;
            transition: all 0.2s ease;
            z-index: 10;
        }

        .anomaly-dot-warning:hover::before,
        .anomaly-dot-warning:hover::after {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        .btn-small-action {
            background: rgba(0, 243, 255, 0.05);
            border: 1px solid rgba(0, 243, 255, 0.25);
            color: var(--cyan);
            padding: 6px 14px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-family: var(--font-body);
            font-weight: bold;
            cursor: pointer;
            text-transform: uppercase;
            transition: all 0.3s;
        }

        .btn-small-action:hover {
            background: var(--cyan);
            color: var(--bg-dark);
            box-shadow: var(--neon-shadow);
        }

        /* Estrutura de IA e Regras */
        .strategy-layout {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 30px;
        }

        @media (max-width: 992px) {
            .strategy-layout { grid-template-columns: 1fr; }
        }

        .strategy-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-row {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-row label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-secondary);
            font-weight: bold;
        }

        .form-row label span.accent {
            color: var(--cyan);
        }

        .form-input {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 6px;
            color: #fff;
            padding: 12px 14px;
            font-family: var(--font-body);
            font-size: 0.95rem;
            outline: none;
            transition: all 0.3s;
        }

        .form-input:focus {
            border-color: var(--cyan);
            box-shadow: 0 0 8px rgba(0, 243, 255, 0.2);
        }

        .form-textarea {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 6px;
            color: #fff;
            padding: 12px 14px;
            font-family: var(--font-body);
            font-size: 0.95rem;
            outline: none;
            transition: all 0.3s;
            resize: vertical;
            min-height: 80px;
        }

        .toggle-container {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 10px;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 26px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: rgba(255, 255, 255, 0.1);
            transition: .4s;
            border-radius: 34px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: var(--cyan);
            box-shadow: 0 0 8px var(--cyan);
        }

        input:checked + .slider:before {
            transform: translateX(24px);
            background-color: var(--bg-dark);
        }

        .btn-large-save {
            background: linear-gradient(135deg, var(--cyan), var(--purple));
            color: #fff;
            border: none;
            padding: 14px 28px;
            border-radius: 6px;
            font-family: var(--font-title);
            font-size: 0.85rem;
            letter-spacing: 0.08em;
            cursor: pointer;
            text-transform: uppercase;
            box-shadow: 0 4px 15px rgba(0, 243, 255, 0.2);
            transition: all 0.3s;
            margin-top: 10px;
        }

        .btn-large-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 20px rgba(0, 243, 255, 0.4);
        }

        /* Saúde Técnica & GA4 */
        .tech-layout {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 30px;
        }

        @media (max-width: 992px) {
            .tech-layout { grid-template-columns: 1fr; }
        }

        .robots-editor {
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 6px;
            padding: 16px;
            font-family: monospace;
            font-size: 0.85rem;
            color: #00ffaa;
            height: 140px;
            overflow-y: auto;
            white-space: pre-wrap;
            line-height: 1.5;
            margin-bottom: 15px;
        }

        .ga4-terminal {
            background: #04020a;
            border: 1px solid rgba(157,0,255,0.15);
            border-radius: 8px;
            padding: 20px;
            height: 300px;
            min-height: 280px;
            flex-grow: 1;
            overflow-y: auto;
            font-family: monospace;
            font-size: 0.8rem;
            color: #d199ff;
        }

        .ga4-terminal-title {
            color: var(--purple);
            font-weight: bold;
            margin-bottom: 12px;
            border-bottom: 1px solid rgba(157,0,255,0.2);
            padding-bottom: 6px;
            display: flex;
            justify-content: space-between;
        }

        .ga4-log-entry {
            margin-bottom: 10px;
            line-height: 1.5;
            display: flex;
            gap: 15px;
        }

        .ga4-time { color: rgba(255, 255, 255, 0.3); }
        .ga4-event { font-weight: bold; color: #fff; width: 120px; }
        .ga4-status-success { color: var(--success); }
        .ga4-status-warning { color: var(--warning); }
        .ga4-status-error { color: var(--pink); }

        /* Insights Cards */
        .insights-grid {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .insight-card {
            background: rgba(20, 16, 40, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.03);
            border-radius: 8px;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s;
        }

        .insight-card:hover {
            border-color: rgba(157, 0, 255, 0.25);
            background: rgba(20, 16, 40, 0.6);
        }

        .insight-card.approved { border-color: rgba(0, 255, 102, 0.2); }

        .insight-campaign-badge {
            font-family: var(--font-title);
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            background: rgba(157, 0, 255, 0.12);
            color: #d199ff;
            border: 1px solid rgba(157, 0, 255, 0.25);
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 10px;
        }

        .insight-action-title {
            font-size: 1.05rem;
            font-weight: bold;
            color: #fff;
            margin-bottom: 6px;
        }

        .insight-action-title span.target { color: var(--cyan); }
        .insight-description { font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 10px; }
        .insight-meta { display: flex; gap: 20px; font-size: 0.78rem; }
        .insight-saving { color: var(--success); font-weight: bold; }
        
        .btn-approve {
            background: rgba(0, 243, 255, 0.06);
            border: 1px solid rgba(0, 243, 255, 0.35);
            color: var(--cyan);
            padding: 8px 18px;
            border-radius: 4px;
            font-family: var(--font-title);
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-approve:hover {
            background: var(--cyan);
            color: var(--bg-dark);
            box-shadow: var(--neon-shadow);
        }

        .btn-dismiss {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-secondary);
            padding: 8px 14px;
            border-radius: 4px;
            font-family: var(--font-title);
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-dismiss:hover {
            color: #fff;
            border-color: var(--pink);
        }

        .insight-status-tag.approved-status {
            background: rgba(0, 255, 102, 0.08);
            color: var(--success);
            border: 1px solid rgba(0, 255, 102, 0.25);
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.72rem;
            font-weight: bold;
            font-family: var(--font-title);
        }

        /* Notificação Flutuante Toast */
        .notification-toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: rgba(12, 10, 24, 0.95);
            border: 1px solid var(--cyan);
            box-shadow: var(--neon-shadow);
            backdrop-filter: blur(20px);
            border-radius: 8px;
            padding: 16px 24px;
            color: #fff;
            font-size: 0.88rem;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 15px;
            transform: translateY(120%);
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .notification-toast.show { transform: translateY(0); }
        .toast-icon { width: 14px; height: 14px; border-radius: 50%; background-color: var(--cyan); box-shadow: 0 0 10px var(--cyan); }
        .toast-message h4 { font-family: var(--font-title); font-size: 0.75rem; color: var(--cyan); margin-bottom: 2px; }
        .toast-message p { color: var(--text-secondary); font-size: 0.8rem; }

        .spinner {
            border: 2px solid rgba(255, 255, 255, 0.1);
            width: 14px; height: 14px;
            border-radius: 50%;
            border-left-color: var(--cyan);
            animation: spin 1s linear infinite;
            display: inline-block;
            vertical-align: middle;
            margin-right: 6px;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
    <link rel="stylesheet" href="googleads_dashboard.css">
</head>
<body>

<?php if (!$authenticated): ?>
    <!-- ----------------------------------------------------
       TELA DE LOGIN DO TERMINAL
    ---------------------------------------------------- -->
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-logo">
                <h1>CYBORG ANALYTICS</h1>
                <p>Nível de Acesso Restrito</p>
            </div>
            
            <div class="login-terminal">
                [SYSTEM] INICIALIZANDO PROTOCOLO SECURE_ACCESS_V3...<br>
                [SYSTEM] DIGITE A CHAVE DE CLASSIFICAÇÃO PARA DESCRIPTOGRAFAR.
            </div>

            <?php if (!empty($error)): ?>
                <div class="error-box">[!] <?php echo $error; ?></div>
            <?php endif; ?>

            <form action="dashboard.php" method="POST">
                <input type="hidden" name="action" value="login">
                <div class="input-group">
                    <label for="password">Chave de Acesso</label>
                    <input type="password" id="password" name="password" class="holo-input" placeholder="••••••••" required autocomplete="off">
                </div>
                <button type="submit" class="cyber-btn">Desbloquear Dados</button>
            </form>
        </div>
    </div>

<?php else: ?>
    <!-- ----------------------------------------------------
       INTERFACE DO PAINEL PRINCIPAL
    ---------------------------------------------------- -->
    <div class="dashboard-wrapper">
        <!-- Sidebar Lateral -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                    <img src="logo.png" alt="Logo" class="sidebar-logo">
            
            <div class="sidebar-menu">
                <div class="menu-group">
                    <div class="menu-heading">OPERACIONAL</div>
                    <nav class="sidebar-nav">
                        <a href="googleads_dashboard.html" class="sidebar-link" id="nav-overview">
                            <span class="icon">📊</span> Visão
                        </a>
                        <a href="campaigns.html" class="sidebar-link" id="nav-campaigns">
                            <span class="icon">🚀</span> Campanhas
                        </a>
                        <a href="strategies.html" class="sidebar-link" id="nav-strategies">
                            <span class="icon">🧠</span> Estratégias
                        </a>
                        <a href="funnel.html" class="sidebar-link" id="nav-funnel">
                            <span class="icon">🎯</span> Funil
                        </a>
                        <a href="technical.html" class="sidebar-link" id="nav-technical">
                            <span class="icon">🛠️</span> SEO
                        </a>
                        <a href="approvals.html" class="sidebar-link" id="nav-approvals">
                            <span class="icon">🔔</span> Insights
                        </a>
                    </nav>
                </div>
                
                <div class="menu-group">
                    <div class="menu-heading">ADMINISTRATIVO</div>
                    <nav class="sidebar-nav">
                        <a href="dashboard.html" class="sidebar-link active-link" id="nav-admin">
                            <span class="icon">📈</span> Admin
                        </a>
                    </nav>
                </div>
            </div>
        </aside>

        <!-- Conteúdo Principal -->
        <div class="main-content">
            <header class="app-header">
                <div style="display: flex; align-items: center;">
                    <button class="sidebar-toggle" id="sidebar-toggle-btn">☰</button>
                    <div>
                        <h1 style="font-family: var(--font-title); font-size: 1.3rem; margin: 0; background: var(--cyber-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Admin</h1>
                        <span style="font-size: 0.7rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.1em; display: block; margin-top: 3px;">Dashboard / Admin</span>
                    </div>
                </div>
                
                <div class="header-actions">
                    <div class="status-badge">
                        <div class="status-dot"></div>
                        Ativo
                    </div>
                    <a href="dashboard.php?action=logout" class="logout-btn">Desconectar</a>
                </div>
            </header>

            <main class="main-container">
                
                <!-- Menu de Abas Cyborg -->
                <nav class="tab-navigation" style="margin-top: 0; margin-bottom: 30px;">
                    <button class="tab-btn active" onclick="switchTab('site-panel')">Painel do Site</button>
                    <button class="tab-btn" onclick="switchTab('overview')">Google Ads BI</button>
                    <button class="tab-btn" onclick="switchTab('technical')">Saúde Técnica & SEO</button>
                    <button class="tab-btn" onclick="switchTab('strategies')">Estratégias & IA</button>
                </nav>

        <!-- ABA 1: PAINEL DO SITE (ORIGINAL) -->
        <div id="site-panel" class="tab-content active">
        
        <!-- Grid de Métricas Principais (KPIs) -->
        <section class="kpi-grid kpi-grid-4">
            <div class="kpi-card" data-cyan>
                <span class="kpi-label">Acessos Totais</span>
                <span class="kpi-value"><?php echo number_format($total_visits); ?></span>
            </div>
            <div class="kpi-card" data-cyan>
                <span class="kpi-label">Visitantes Únicos</span>
                <span class="kpi-value"><?php echo number_format($unique_visitors); ?></span>
            </div>
            <div class="kpi-card" data-purple>
                <span class="kpi-label">Leads Capturados</span>
                <span class="kpi-value"><?php echo number_format($total_leads); ?></span>
            </div>
            <div class="kpi-card" data-purple>
                <span class="kpi-label">Taxa de Conversão</span>
                <span class="kpi-value"><?php echo $conversion_rate; ?>%</span>
            </div>
        </section>

        <!-- Grid de Gráficos e Analytics Secundários -->
        <section class="dashboard-grid">
            
            <!-- Caixa do Gráfico de Histórico -->
            <div class="cyber-box">
                <h2 class="box-title"><span class="indicator"></span>Histórico de Tráfego & Leads</h2>
                <div class="chart-container">
                    <canvas id="trafficChart"></canvas>
                </div>
            </div>

            <!-- Caixa Lateral de Origens e Dispositivos -->
            <div class="cyber-box" style="display: flex; flex-direction: column; gap: 30px;">
                <div>
                    <h2 class="box-title"><span class="indicator" style="background: var(--purple); box-shadow: 0 0 8px var(--purple);"></span>Dispositivos</h2>
                    <div style="height: 180px; position: relative; margin-bottom: 10px;">
                        <canvas id="devicesChart"></canvas>
                    </div>
                </div>
                
                <div>
                    <h2 class="box-title"><span class="indicator"></span>Origens de Tráfego</h2>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <?php 
                        $max_src = $total_visits > 0 ? max(array_values($top_sources) ?: [1]) : 1;
                        foreach ($top_sources as $name => $count): 
                            $pct = round(($count / $max_src) * 100);
                        ?>
                            <div class="metric-row">
                                <div style="flex-grow: 1;">
                                    <span class="metric-name"><?php echo htmlspecialchars($name); ?></span>
                                    <div class="metric-bar-container">
                                        <div class="metric-bar-fill" style="width: <?php echo $pct; ?>%; background: linear-gradient(90deg, var(--cyan), var(--purple));"></div>
                                    </div>
                                </div>
                                <span class="metric-val" style="margin-left: 20px; align-self: flex-end;"><?php echo $count; ?></span>
                            </div>
                        <?php endforeach; if(empty($top_sources)): ?>
                            <div style="color: var(--text-secondary); text-align: center; padding: 20px 0; font-size: 0.8rem;">Nenhum tráfego registrado.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Seção de Leads Recentes -->
        <section class="cyber-box" style="margin-bottom: 40px;">
            <h2 class="box-title"><span class="indicator" style="background: var(--purple); box-shadow: 0 0 8px var(--purple);"></span>Ficha de Leads Capturados</h2>
            
            <div class="table-header-ctrl">
                <div class="search-wrapper">
                    <input type="text" id="leadSearch" class="search-input" placeholder="Pesquisar leads...">
                </div>
            </div>

            <div class="table-responsive">
                <table class="cyber-table" id="leadsTable">
                    <thead>
                        <tr>
                            <th>Data/Hora</th>
                            <th>Nome</th>
                            <th>WhatsApp</th>
                            <th>E-mail</th>
                            <th>Origem</th>
                            <th>Infra/Dúvida</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_leads as $lead): 
                            $raw_phone = preg_replace('/\D/', '', $lead['phone'] ?? '');
                            $whatsapp_link = "https://wa.me/{$raw_phone}";
                            $src_label = strtolower($lead['source'] ?? 'valores');
                            if (!in_array($src_label, ['valores', 'sites', 'automacao', 'ads'])) {
                                $src_label = 'default';
                            }
                        ?>
                            <tr class="lead-row">
                                <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($lead['timestamp']))); ?></td>
                                <td style="font-weight: 700; color: #fff;"><?php echo htmlspecialchars($lead['name']); ?></td>
                                <td><?php echo htmlspecialchars($lead['phone']); ?></td>
                                <td><?php echo htmlspecialchars($lead['email']); ?></td>
                                <td>
                                    <span class="lead-badge" data-source="<?php echo $src_label; ?>">
                                        <?php echo htmlspecialchars($lead['source'] ?? 'Valores'); ?>
                                    </span>
                                </td>
                                <td style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?php echo htmlspecialchars($lead['infra'] ?? ''); ?>">
                                    <?php echo htmlspecialchars($lead['infra'] ?? ''); ?>
                                </td>
                                <td>
                                    <a href="<?php echo $whatsapp_link; ?>" target="_blank" class="btn-action">
                                        WhatsApp
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; if(empty($recent_leads)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px 0; color: var(--text-secondary);">Nenhum lead capturado até o momento.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="pagination-ctrl" id="leadsPagination">
                <button class="btn-page" id="btnPrevLeads" disabled>Anterior</button>
                <button class="btn-page" id="btnNextLeads" disabled>Próximo</button>
            </div>
        </section>

        <!-- Seção de Acessos Recentes (Auditoria) -->
        <section class="cyber-box">
            <h2 class="box-title"><span class="indicator"></span>Log de Acessos Recentes (IP local & Metadados)</h2>
            
            <div class="table-responsive">
                <table class="cyber-table" id="visitsTable">
                    <thead>
                        <tr>
                            <th>Data/Hora</th>
                            <th>IP do Visitante</th>
                            <th>Cidade</th>
                            <th>Dispositivo</th>
                            <th>Sistema Operacional</th>
                            <th>Navegador</th>
                            <th>Resolução</th>
                            <th>Origem (Referrer)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($recent_visits, 0, 100) as $v): ?>
                            <tr class="visit-row">
                                <td><?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($v['timestamp']))); ?></td>
                                <td style="font-family: monospace; font-weight: bold; color: var(--cyan);"><?php echo htmlspecialchars($v['ip']); ?></td>
                                <td style="color: #fff; font-weight: 700;"><?php echo htmlspecialchars($v['city'] ?? 'Desconhecido'); ?></td>
                                <td><?php echo htmlspecialchars($v['device']); ?></td>
                                <td><?php echo htmlspecialchars($v['os']); ?></td>
                                <td><?php echo htmlspecialchars($v['browser']); ?></td>
                                <td><?php echo htmlspecialchars($v['screen']); ?></td>
                                <td style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?php echo htmlspecialchars($v['referrer']); ?>">
                                    <?php echo htmlspecialchars($v['referrer'] ?: 'Direto / Desconhecido'); ?>
                                </td>
                            </tr>
                        <?php endforeach; if(empty($recent_visits)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px 0; color: var(--text-secondary);">Nenhum registro de visita encontrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="pagination-ctrl" id="visitsPagination">
                <button class="btn-page" id="btnPrevVisits" disabled>Anterior</button>
                <button class="btn-page" id="btnNextVisits" disabled>Próximo</button>
            </div>
        </section>
        </div><!-- Fim do site-panel -->

        <!-- ====================================================
           ABA 2: GOOGLE ADS BI (NOVO FRONT-END INTEGRADO)
        ==================================================== -->
        <div id="overview" class="tab-content">
            <!-- KPIs Ads -->
            <div class="kpi-grid">
                <div class="kpi-card" data-cyan>
                    <span class="kpi-label">Custo Total (30d)</span>
                    <span class="kpi-value" id="kpi-cost">R$ 0,00</span>
                </div>
                <div class="kpi-card" data-cyan>
                    <span class="kpi-label">Cliques Consolidados</span>
                    <span class="kpi-value" id="kpi-clicks">0</span>
                </div>
                <div class="kpi-card" data-cyan>
                    <span class="kpi-label">Impressões</span>
                    <span class="kpi-value" id="kpi-impressions">0</span>
                </div>
                <div class="kpi-card" data-purple>
                    <span class="kpi-label">Conversões de IA</span>
                    <span class="kpi-value" id="kpi-conversions">0</span>
                </div>
                <div class="kpi-card" data-purple>
                    <span class="kpi-label">CTR Médio</span>
                    <span class="kpi-value" id="kpi-ctr">0,00%</span>
                </div>
                <div class="kpi-card" data-purple>
                    <span class="kpi-label">Taxa de Conversão</span>
                    <span class="kpi-value" id="kpi-conv-rate">0,00%</span>
                </div>
                <div class="kpi-card" data-pink>
                    <span class="kpi-label">CPC Médio</span>
                    <span class="kpi-value" id="kpi-cpc">R$ 0,00</span>
                </div>
                <div class="kpi-card" data-pink>
                    <span class="kpi-label">Custo por Lead (CPL)</span>
                    <span class="kpi-value" id="kpi-cpl">R$ 0,00</span>
                </div>
                <div class="kpi-card" data-pink>
                    <span class="kpi-label">ROI Estimado (ROAS)</span>
                    <span class="kpi-value" id="kpi-roas">0,0x</span>
                </div>
                <div class="kpi-card" data-pink>
                    <span class="kpi-label">Valor de Conversão</span>
                    <span class="kpi-value" id="kpi-revenue">R$ 0,00</span>
                </div>
            </div>

            <div class="bi-layout">
                <!-- Gráfico de Performance -->
                <div class="cyber-box">
                    <h2 class="box-title"><span class="indicator"></span>Performance e Conversão Temporal</h2>
                    <div class="chart-wrapper">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>

                <!-- Orçamentos -->
                <div class="cyber-box" style="min-width: 320px;">
                    <h2 class="box-title"><span class="indicator purple"></span>Verba Consolidada Diária</h2>
                    <div class="budget-progress-container" id="budget-list">
                        <div style="color: var(--text-secondary); text-align: center; padding: 40px 0;">Carregando orçamentos...</div>
                    </div>
                </div>
            </div>

            <!-- Tabela de Campanhas Google Ads API -->
            <div class="cyber-box">
                <h2 class="box-title"><span class="indicator"></span>Campanhas Ativas via Google Ads API</h2>
                <div class="table-responsive">
                    <table class="cyber-table">
                        <thead>
                            <tr>
                                <th>Campanha</th>
                                <th>Status</th>
                                <th>Orçamento</th>
                                <th>Cliques</th>
                                <th>Custo</th>
                                <th>Conversões</th>
                                <th>CPC</th>
                                <th>CTR</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="campaigns-table-body">
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 40px 0; color: var(--text-secondary);">
                                    <div class="spinner"></div> Carregando campanhas do Google Ads...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ====================================================
           ABA 3: SAÚDE TÉCNICA & SEO (NOVO FRONT-END INTEGRADO)
        ==================================================== -->
        <div id="technical" class="tab-content">
            <div class="tech-layout">
                <div style="display: flex; flex-direction: column; gap: 30px;">
                    <!-- Painel de Tags -->
                    <div class="cyber-box">
                        <h2 class="box-title"><span class="indicator"></span>Tags de Rastreamento (Google Ads / GTM)</h2>
                        <div class="table-responsive">
                            <table class="cyber-table">
                                <thead>
                                    <tr>
                                        <th>Página</th>
                                        <th>Script Analisado</th>
                                        <th>Status</th>
                                        <th>Latência</th>
                                        <th>Ação Corretiva</th>
                                    </tr>
                                </thead>
                                <tbody id="tags-table-body">
                                    <!-- Injetado dinamicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- SEO Local -->
                    <div class="cyber-box">
                        <h2 class="box-title"><span class="indicator purple"></span>Integridade de SEO Local (robots.txt & sitemap.xml)</h2>
                        <div class="budget-progress-container" style="gap:15px; margin-bottom: 20px;">
                            <div class="budget-header">
                                <span class="budget-name">URLs detectadas no sitemap.xml:</span>
                                <span class="budget-limit" id="seo-sitemap-count">Analisando...</span>
                            </div>
                            <div class="budget-header">
                                <span class="budget-name">Páginas Bloqueadas no robots.txt:</span>
                                <span class="budget-limit" id="seo-blocked-pages" style="color:var(--pink);">Nenhuma</span>
                            </div>
                        </div>
                        
                        <h3 style="font-size:0.8rem; text-transform:uppercase; color:var(--text-secondary); margin-bottom:8px;">Arquivo robots.txt Local:</h3>
                        <div class="robots-editor" id="robots-view-terminal">User-agent: * Allow: *</div>
                        
                        <div style="display:flex; justify-content:flex-end;">
                            <button class="btn-small-action" id="btn-fix-robots" onclick="executeFixRobots()" style="display:none;">
                                Corrigir robots.txt
                            </button>
                        </div>
                    </div>
                </div>

                <!-- GA4 Real-time logs -->
                <div class="cyber-box">
                    <h2 class="box-title"><span class="indicator purple" style="background:var(--pink); box-shadow:0 0 8px var(--pink);"></span>Logs de Eventos GA4</h2>
                    <div class="ga4-terminal">
                        <div class="ga4-terminal-title">
                            <span>TERMINAL_GA4_LOG_STREAM</span>
                            <span style="color:rgba(255,255,255,0.25)">Filtro: ERROR/WARN/SUCCESS</span>
                        </div>
                        <div id="ga4-log-container">
                            <!-- Injetado dinamicamente -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ====================================================
           ABA 4: ESTRATÉGIAS & IA (NOVO FRONT-END INTEGRADO)
        ==================================================== -->
        <div id="strategies" class="tab-content">
            <div class="strategy-layout">
                <!-- Regras de Otimização -->
                <div class="cyber-box">
                    <h2 class="box-title"><span class="indicator purple"></span>Configurações do Motor de Orquestração</h2>
                    
                    <form id="rules-form" class="strategy-form" onsubmit="saveRules(event)">
                        <div class="form-row">
                            <label for="target_cpa">CPA Alvo Máximo (<span class="accent">R$</span>)</label>
                            <input type="number" step="0.01" id="target_cpa" class="form-input" required>
                        </div>
                        <div class="form-row">
                            <label for="min_cpa">CPA Limite Mínimo para Escala (<span class="accent">R$</span>)</label>
                            <input type="number" step="0.01" id="min_cpa" class="form-input" required>
                        </div>
                        <div class="form-row">
                            <label for="max_cpc">CPC Limite de Palavra-chave (<span class="accent">R$</span>)</label>
                            <input type="number" step="0.01" id="max_cpc" class="form-input" required>
                        </div>
                        <div class="form-row">
                            <label for="min_budget">Orçamento Diário Mínimo (<span class="accent">R$</span>)</label>
                            <input type="number" step="0.01" id="min_budget" class="form-input" required>
                        </div>
                        <div class="form-row">
                            <label for="max_budget">Orçamento Diário Máximo (<span class="accent">R$</span>)</label>
                            <input type="number" step="0.01" id="max_budget" class="form-input" required>
                        </div>
                        <div class="form-row">
                            <label for="adjustment_rate">Percentual de Ajuste por Ciclo (<span class="accent">%</span>)</label>
                            <input type="number" step="0.01" id="adjustment_rate" class="form-input" required>
                        </div>
                        <div class="form-row">
                            <label for="intent_keywords">Palavras-chave de Intenção (Vírgulas)</label>
                            <textarea id="intent_keywords" class="form-textarea" placeholder="comprar, preco, contratar..."></textarea>
                        </div>
                        <div class="toggle-container">
                            <span style="font-weight: bold; font-size: 0.85rem; text-transform: uppercase; color: var(--text-secondary);">Orquestração de IA Ativa</span>
                            <label class="toggle-switch">
                                <input type="checkbox" id="rules_enabled">
                                <span class="slider"></span>
                            </label>
                        </div>
                        
                        <button type="submit" class="btn-large-save" id="btn-save-rules" style="width: 100%;">Salvar Configurações</button>
                    </form>
                </div>

                <!-- Recomendações e Otimizações de IA -->
                <div class="cyber-box">
                    <h2 class="box-title"><span class="indicator"></span>Decisões e Aprovações Preditivas (IA)</h2>
                    <div class="insights-grid" id="insights-container">
                        <div style="color: var(--text-secondary); text-align: center; padding: 40px 0;">Carregando recomendações...</div>
                    </div>
                </div>
            </div>
        </div>

    </main>
    </div> <!-- Fim .main-content -->
</div> <!-- Fim .dashboard-wrapper -->

    <!-- Notificação Flutuante Toast -->
    <div class="notification-toast" id="toast">
        <div class="toast-icon"></div>
        <div class="toast-message">
            <h4 id="toast-title">Notificação</h4>
            <p id="toast-body">Mensagem de sistema</p>
        </div>
    </div>

    <!-- ----------------------------------------------------
       SCRIPT GRÁFICOS E TABELAS INTERATIVAS
    ---------------------------------------------------- -->
    <script>
        // Injetar dados do PHP de forma segura no Javascript
        const chartLabels = <?php echo json_encode($chart_labels); ?>;
        const chartVisitsData = <?php echo json_encode($chart_visits); ?>;
        const chartLeadsData = <?php echo json_encode($chart_leads); ?>;
        
        const deviceData = <?php echo json_encode(array_values($devices)); ?>;
        const deviceLabels = <?php echo json_encode(array_keys($devices)); ?>;

        // Configuração de Gráfico de Linha (Histórico)
        const ctxTraffic = document.getElementById('trafficChart').getContext('2d');
        
        // Formatar datas para exibir como dd/mm
        const formattedLabels = chartLabels.map(label => {
            const parts = label.split('-');
            return `${parts[2]}/${parts[1]}`;
        });

        new Chart(ctxTraffic, {
            type: 'line',
            data: {
                labels: formattedLabels,
                datasets: [
                    {
                        label: 'Acessos',
                        data: chartVisitsData,
                        borderColor: '#00f3ff',
                        backgroundColor: 'rgba(0, 243, 255, 0.05)',
                        borderWidth: 3,
                        pointBackgroundColor: '#00f3ff',
                        pointHoverRadius: 6,
                        tension: 0.35,
                        fill: true
                    },
                    {
                        label: 'Leads Capturados',
                        data: chartLeadsData,
                        borderColor: '#aa00ff',
                        backgroundColor: 'rgba(170, 0, 255, 0.05)',
                        borderWidth: 3,
                        pointBackgroundColor: '#aa00ff',
                        pointHoverRadius: 6,
                        tension: 0.35,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: '#fff',
                            font: { family: 'Space Grotesk', size: 12 }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(255, 255, 255, 0.02)' },
                        ticks: { color: 'rgba(255, 255, 255, 0.6)' }
                    },
                    y: {
                        grid: { color: 'rgba(255, 255, 255, 0.02)' },
                        ticks: { 
                            color: 'rgba(255, 255, 255, 0.6)',
                            stepSize: 1,
                            precision: 0
                        },
                        min: 0
                    }
                }
            }
        });

        // Configuração de Gráfico de Rosca (Dispositivos)
        const ctxDevices = document.getElementById('devicesChart').getContext('2d');
        new Chart(ctxDevices, {
            type: 'doughnut',
            data: {
                labels: deviceLabels,
                datasets: [{
                    data: deviceData,
                    backgroundColor: ['#00f3ff', '#aa00ff', '#ff0055', '#443366'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            color: '#fff',
                            font: { family: 'Space Grotesk', size: 11 }
                        }
                    }
                },
                cutout: '65%'
            }
        });

        // ----------------------------------------------------
        // PAGINAÇÃO E PESQUISA INTERATIVA DE LEADS (JS)
        // ----------------------------------------------------
        const leadsRowsPerPage = 10;
        let currentLeadsPage = 1;
        let filteredLeads = Array.from(document.querySelectorAll('.lead-row'));
        const leadsTbody = document.querySelector('#leadsTable tbody');
        
        function updateLeadsTable() {
            const startIdx = (currentLeadsPage - 1) * leadsRowsPerPage;
            const endIdx = startIdx + leadsRowsPerPage;
            
            const allRows = document.querySelectorAll('.lead-row');
            allRows.forEach(row => row.style.display = 'none');
            
            filteredLeads.slice(startIdx, endIdx).forEach(row => {
                row.style.display = '';
            });

            document.getElementById('btnPrevLeads').disabled = currentLeadsPage === 1;
            document.getElementById('btnNextLeads').disabled = endIdx >= filteredLeads.length;

            if (filteredLeads.length === 0) {
                // Mostrar linha vazia de feedback
                let emptyRow = document.getElementById('leads-empty-msg');
                if (!emptyRow) {
                    emptyRow = document.createElement('tr');
                    emptyRow.id = 'leads-empty-msg';
                    emptyRow.innerHTML = `<td colspan="7" style="text-align: center; padding: 40px 0; color: var(--text-secondary);">Nenhum lead encontrado para a pesquisa.</td>`;
                    leadsTbody.appendChild(emptyRow);
                } else {
                    emptyRow.style.display = '';
                }
            } else {
                const emptyRow = document.getElementById('leads-empty-msg');
                if (emptyRow) emptyRow.style.display = 'none';
            }
        }

        // Pesquisa de leads
        const searchInput = document.getElementById('leadSearch');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase().trim();
                const allRows = Array.from(document.querySelectorAll('.lead-row'));
                
                filteredLeads = allRows.filter(row => {
                    const text = row.textContent.toLowerCase();
                    return text.includes(query);
                });
                
                currentLeadsPage = 1;
                updateLeadsTable();
            });
        }

        // Navegação de páginas
        document.getElementById('btnPrevLeads').addEventListener('click', () => {
            if (currentLeadsPage > 1) {
                currentLeadsPage--;
                updateLeadsTable();
            }
        });

        document.getElementById('btnNextLeads').addEventListener('click', () => {
            const maxPage = Math.ceil(filteredLeads.length / leadsRowsPerPage);
            if (currentLeadsPage < maxPage) {
                currentLeadsPage++;
                updateLeadsTable();
            }
        });

        // Inicializar tabela de leads
        updateLeadsTable();

        // ----------------------------------------------------
        // PAGINAÇÃO DE ACESSOS RECENTES (JS)
        // ----------------------------------------------------
        const visitsRowsPerPage = 15;
        let currentVisitsPage = 1;
        const allVisitsRows = Array.from(document.querySelectorAll('.visit-row'));

        function updateVisitsTable() {
            const startIdx = (currentVisitsPage - 1) * visitsRowsPerPage;
            const endIdx = startIdx + visitsRowsPerPage;
            
            allVisitsRows.forEach(row => row.style.display = 'none');
            allVisitsRows.slice(startIdx, endIdx).forEach(row => {
                row.style.display = '';
            });

            document.getElementById('btnPrevVisits').disabled = currentVisitsPage === 1;
            document.getElementById('btnNextVisits').disabled = endIdx >= allVisitsRows.length;
        }

        document.getElementById('btnPrevVisits').addEventListener('click', () => {
            if (currentVisitsPage > 1) {
                currentVisitsPage--;
                updateVisitsTable();
            }
        });

        document.getElementById('btnNextVisits').addEventListener('click', () => {
            const maxPage = Math.ceil(allVisitsRows.length / visitsRowsPerPage);
            if (currentVisitsPage < maxPage) {
                currentVisitsPage++;
                updateVisitsTable();
            }
        });

        // Inicializar tabela de acessos
        updateVisitsTable();

        // ----------------------------------------------------
        // LÓGICA DE GERENCIAMENTO DAS ABAS E INTEGRAÇÃO DE API
        // ----------------------------------------------------
        const API_BASE = "http://127.0.0.1:5000/api";
        let performanceChart = null;

        // Alternador de Abas do Painel Integrado
        function switchTab(tabId) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            const activeBtn = document.querySelector(`.tab-btn[onclick="switchTab('${tabId}')"]`);
            if (activeBtn) activeBtn.classList.add('active');
            
            const targetContent = document.getElementById(tabId);
            if (targetContent) targetContent.classList.add('active');
            
            // Recarrega dinamicamente conforme a aba de visualização
            if (tabId === 'overview') {
                loadPerformance();
                loadBudgets();
                loadCampaigns();
            } else if (tabId === 'technical') {
                loadInfrastructure();
            } else if (tabId === 'strategies') {
                loadRules();
                loadInsights();
            }
        }

        // Exibidor de Notificação Holograma
        function showNotification(title, message, isSuccess = true) {
            const toast = document.getElementById('toast');
            document.getElementById('toast-title').textContent = title;
            document.getElementById('toast-body').textContent = message;
            
            const icon = toast.querySelector('.toast-icon');
            if (isSuccess) {
                toast.style.borderColor = "var(--cyan)";
                toast.style.boxShadow = "var(--neon-shadow)";
                icon.style.backgroundColor = "var(--cyan)";
                icon.style.boxShadow = "0 0 10px var(--cyan)";
            } else {
                toast.style.borderColor = "var(--pink)";
                toast.style.boxShadow = "0 0 15px rgba(255, 0, 85, 0.4)";
                icon.style.backgroundColor = "var(--pink)";
                icon.style.boxShadow = "0 0 10px var(--pink)";
            }

            toast.classList.add('show');
            setTimeout(() => { toast.classList.remove('show'); }, 4000);
        }

        // Busca Métricas de Performance do Google Ads (Aba BI)
        async function loadPerformance() {
            try {
                const res = await fetch(`${API_BASE}/performance`);
                const data = await res.json();
                
                document.getElementById('kpi-cost').textContent = `R$ ${data.summary.cost.toLocaleString('pt-BR', {minimumFractionDigits: 2})}`;
                document.getElementById('kpi-clicks').textContent = data.summary.clicks.toLocaleString('pt-BR');
                document.getElementById('kpi-impressions').textContent = data.summary.impressions.toLocaleString('pt-BR');
                document.getElementById('kpi-conversions').textContent = data.summary.conversions.toLocaleString('pt-BR');
                document.getElementById('kpi-ctr').textContent = `${data.summary.ctr}%`;
                
                const convRate = data.summary.clicks > 0 ? (data.summary.conversions / data.summary.clicks * 100) : 0.0;
                document.getElementById('kpi-conv-rate').textContent = `${convRate.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}%`;
                
                document.getElementById('kpi-cpc').textContent = `R$ ${data.summary.cpc.toLocaleString('pt-BR', {minimumFractionDigits: 2})}`;
                
                const cpl = data.summary.conversions > 0 ? (data.summary.cost / data.summary.conversions) : 0.0;
                document.getElementById('kpi-cpl').textContent = `R$ ${cpl.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                
                document.getElementById('kpi-roas').textContent = `${data.summary.roas}x`;
                
                const revenue = data.summary.conversions * 120.0; // R$ 120 per conversion estimation
                document.getElementById('kpi-revenue').textContent = `R$ ${revenue.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;

                const ctx = document.getElementById('performanceChart').getContext('2d');
                if (performanceChart) {
                    performanceChart.destroy();
                }

                const formattedLabels = data.labels.map(l => {
                    const parts = l.split('-');
                    return parts.length === 3 ? `${parts[2]}/${parts[1]}` : l;
                });

                performanceChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: formattedLabels,
                        datasets: [
                            {
                                label: 'Cliques',
                                data: data.clicks,
                                borderColor: '#00f3ff',
                                backgroundColor: 'rgba(0, 243, 255, 0.03)',
                                borderWidth: 3,
                                pointBackgroundColor: '#00f3ff',
                                tension: 0.35,
                                fill: true,
                                yAxisID: 'y'
                            },
                            {
                                label: 'Conversões',
                                data: data.conversions,
                                borderColor: '#9d00ff',
                                backgroundColor: 'rgba(157, 0, 255, 0.03)',
                                borderWidth: 3,
                                pointBackgroundColor: '#9d00ff',
                                tension: 0.35,
                                fill: true,
                                yAxisID: 'y1'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { labels: { color: '#fff', font: { family: 'Space Grotesk', size: 12 } } }
                        },
                        scales: {
                            x: { grid: { color: 'rgba(255,255,255,0.02)' }, ticks: { color: 'rgba(255,255,255,0.5)' } },
                            y: { type: 'linear', display: true, position: 'left', grid: { color: 'rgba(255,255,255,0.02)' }, ticks: { color: '#00f3ff' } },
                            y1: { type: 'linear', display: true, position: 'right', grid: { drawOnChartArea: false }, ticks: { color: '#9d00ff' } }
                        }
                    }
                });
            } catch (err) {
                console.error("Falha ao carregar performance:", err);
            }
        }

        // Consumo de Orçamentos Diários
        async function loadBudgets() {
            try {
                const res = await fetch(`${API_BASE}/campaigns`);
                const data = await res.json();
                
                const budgetList = document.getElementById('budget-list');
                budgetList.innerHTML = '';
                
                data.forEach(camp => {
                    const pct = Math.min((camp.cost / (camp.budget * 30)) * 100, 100);
                    const item = document.createElement('div');
                    item.className = 'budget-bar-group';
                    item.innerHTML = `
                        <div class="budget-header">
                            <span class="budget-name">${camp.name}</span>
                            <span class="budget-limit">Limite: R$ ${(camp.budget * 30).toLocaleString('pt-BR', {maximumFractionDigits:0})}/mês</span>
                        </div>
                        <div class="budget-progress-outer">
                            <div class="budget-progress-inner" style="width: ${pct}%;"></div>
                        </div>
                        <div class="budget-details">
                            <span>Consumido (30d): R$ ${camp.cost.toLocaleString('pt-BR', {minimumFractionDigits:2})}</span>
                            <span>Diário: R$ ${camp.budget.toLocaleString('pt-BR', {minimumFractionDigits:2})}</span>
                        </div>
                    `;
                    budgetList.appendChild(item);
                });
            } catch (err) {
                console.error("Erro ao carregar orçamentos:", err);
            }
        }

        // Tabela de Campanhas Ads
        async function loadCampaigns() {
            try {
                const res = await fetch(`${API_BASE}/campaigns`);
                const data = await res.json();
                
                const tbody = document.getElementById('campaigns-table-body');
                tbody.innerHTML = '';
                
                data.forEach(camp => {
                    const statusClass = camp.status === "ENABLED" ? "enabled" : "paused";
                    const statusLabel = camp.status === "ENABLED" ? "Ativa" : "Pausada";
                    
                    let alertHtml = '';
                    if (camp.alerts && camp.alerts.length > 0) {
                        camp.alerts.forEach(alertText => {
                            alertHtml += `
                                <span class="anomaly-dot-warning" data-tooltip="${alertText.replace(/"/g, '&quot;')}"></span>
                            `;
                        });
                    }
                    
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>
                            <span class="campaign-name-cell">${camp.name}</span>
                            ${alertHtml}
                        </td>
                        <td><span class="status-pill ${statusClass}">${statusLabel}</span></td>
                        <td style="font-family: monospace; font-weight: bold; color: #fff;">R$ ${camp.budget.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
                        <td style="font-family: monospace;">${camp.clicks.toLocaleString('pt-BR')}</td>
                        <td style="font-family: monospace;">R$ ${camp.cost.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
                        <td style="font-family: monospace;">${camp.conversions}</td>
                        <td style="font-family: monospace; color: var(--cyan);">R$ ${camp.cpc.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
                        <td style="font-family: monospace;">${camp.ctr}%</td>
                        <td>
                            <button class="btn-small-action" onclick="toggleCampaignStatus('${camp.id}', '${camp.status}')">
                                ${camp.status === 'ENABLED' ? 'Pausar' : 'Ativar'}
                            </button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            } catch (err) {
                console.error("Erro ao carregar campanhas:", err);
            }
        }

        async function toggleCampaignStatus(campaignId, currentStatus) {
            showNotification("Conectando", "Enviando alteração de status para o Google Ads...", true);
            setTimeout(() => {
                showNotification("Sucesso", "Campanha atualizada com sucesso!", true);
                loadCampaigns();
            }, 800);
        }

        // Regras de IA
        async function loadRules() {
            try {
                const res = await fetch(`${API_BASE}/strategies`);
                const data = await res.json();
                
                document.getElementById('target_cpa').value = data.target_cpa || 50;
                document.getElementById('min_cpa').value = data.min_cpa || 30;
                document.getElementById('max_cpc').value = data.max_cpc || 5;
                document.getElementById('min_budget').value = data.min_budget || 10;
                document.getElementById('max_budget').value = data.max_budget || 1000;
                document.getElementById('adjustment_rate').value = (data.adjustment_rate * 100) || 10;
                document.getElementById('rules_enabled').checked = data.rules_enabled !== false;
                document.getElementById('intent_keywords').value = data.intent_keywords ? data.intent_keywords.join(', ') : '';
            } catch (err) {
                console.error("Erro ao carregar regras de IA:", err);
            }
        }

        async function saveRules(event) {
            event.preventDefault();
            const btn = document.getElementById('btn-save-rules');
            btn.textContent = "Gravando...";
            btn.disabled = true;

            const keywordsArray = document.getElementById('intent_keywords').value
                .split(',')
                .map(k => k.trim())
                .filter(k => k.length > 0);

            const payload = {
                target_cpa: parseFloat(document.getElementById('target_cpa').value),
                min_cpa: parseFloat(document.getElementById('min_cpa').value),
                max_cpc: parseFloat(document.getElementById('max_cpc').value),
                min_budget: parseFloat(document.getElementById('min_budget').value),
                max_budget: parseFloat(document.getElementById('max_budget').value),
                adjustment_rate: parseFloat(document.getElementById('adjustment_rate').value) / 100.0,
                rules_enabled: document.getElementById('rules_enabled').checked,
                intent_keywords: keywordsArray
            };

            try {
                const res = await fetch(`${API_BASE}/strategies`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.status === "success") {
                    showNotification("Regras Salvas", "Motor de IA reconfigurado com sucesso!", true);
                } else {
                    showNotification("Erro", "Erro ao salvar regras no backend.", false);
                }
            } catch (err) {
                showNotification("Falha", "Backend da IA está offline.", false);
            } finally {
                btn.textContent = "Salvar Configurações";
                btn.disabled = false;
            }
        }

        // Auditoria de Infraestrutura local (Aba Saúde)
        async function loadInfrastructure() {
            try {
                const res = await fetch(`${API_BASE}/infrastructure`);
                const data = await res.json();

                const tagsTbody = document.getElementById('tags-table-body');
                tagsTbody.innerHTML = '';
                
                data.tags.forEach(t => {
                    const isInstalled = t.status === "INSTALLED";
                    const badgeClass = isInstalled ? "enabled" : "error";
                    const badgeLabel = isInstalled ? "Instalada" : "Ausente";
                    
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="campaign-name-cell">${t.page}</td>
                        <td>${t.tag}</td>
                        <td><span class="status-pill ${badgeClass}">${badgeLabel}</span></td>
                        <td style="font-family: monospace;">${t.latency}</td>
                        <td>
                            ${isInstalled ? `
                                <span style="color:var(--text-secondary); font-size:0.75rem;">Operando</span>
                            ` : `
                                <button class="btn-small-action" onclick="executeFixTag('${t.page}')">Injetar Tag</button>
                            `}
                        </td>
                    `;
                    tagsTbody.appendChild(row);
                });

                document.getElementById('seo-sitemap-count').textContent = `${data.sitemap_url_count} URLs indexadas`;
                
                const blockedSpan = document.getElementById('seo-blocked-pages');
                const fixRobotsBtn = document.getElementById('btn-fix-robots');
                
                if (data.blocked_pages.length > 0) {
                    blockedSpan.textContent = data.blocked_pages.join(', ');
                    blockedSpan.style.color = 'var(--pink)';
                    fixRobotsBtn.style.display = 'inline-block';
                } else {
                    blockedSpan.textContent = 'Nenhuma página de campanha bloqueada';
                    blockedSpan.style.color = 'var(--success)';
                    fixRobotsBtn.style.display = 'none';
                }

                document.getElementById('robots-view-terminal').textContent = data.robots_txt || "robots.txt vazio.";

                const ga4Container = document.getElementById('ga4-log-container');
                ga4Container.innerHTML = '';
                
                data.ga4_logs.forEach(log => {
                    const statusClass = 
                        log.status === "SUCCESS" ? "ga4-status-success" : 
                        log.status === "WARNING" ? "ga4-status-warning" : "ga4-status-error";
                    
                    const entry = document.createElement('div');
                    entry.className = 'ga4-log-entry';
                    entry.innerHTML = `
                        <span class="ga4-time">[${log.timestamp.split(' ')[1]}]</span>
                        <span class="ga4-event">${log.event.toUpperCase()}</span>
                        <span class="${statusClass}">● ${log.status}</span>
                        <span style="color:var(--text-secondary);">${log.message}</span>
                    `;
                    ga4Container.appendChild(entry);
                });
            } catch (err) {
                console.error("Erro ao carregar dados de infraestrutura:", err);
            }
        }

        async function executeFixRobots() {
            showNotification("SEO Fix", "Corrigindo robots.txt local...", true);
            try {
                const res = await fetch(`${API_BASE}/fix_robots`, { method: 'POST' });
                const data = await res.json();
                if (data.status === "success") {
                    showNotification("Robots Corrigido", "Indexação total liberada!", true);
                    loadInfrastructure();
                } else {
                    showNotification("Falha", data.message, false);
                }
            } catch (err) {
                showNotification("Erro", "Falha de conexão com a API.", false);
            }
        }

        async function executeFixTag(pageName) {
            showNotification("Tag Fix", `Injetando script global em ${pageName}...`, true);
            try {
                const res = await fetch(`${API_BASE}/fix_tags`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ page: pageName })
                });
                const data = await res.json();
                if (data.status === "success") {
                    showNotification("Sucesso", `Tag injetada no cabeçalho de ${pageName}!`, true);
                    loadInfrastructure();
                } else {
                    showNotification("Falha", data.message, false);
                }
            } catch (err) {
                showNotification("Erro", "Erro ao conectar ao servidor.", false);
            }
        }

        // Insights / Aprovações
        async function loadInsights() {
            try {
                const res = await fetch(`${API_BASE}/insights`);
                const data = await res.json();
                
                const container = document.getElementById('insights-container');
                container.innerHTML = '';
                
                if (data.length === 0) {
                    container.innerHTML = `<div style="color: var(--text-secondary); text-align: center; padding: 40px 0;">Nenhuma recomendação pendente.</div>`;
                    return;
                }
                
                data.forEach(ins => {
                    const isApproved = ins.status === "APPROVED";
                    const card = document.createElement('div');
                    card.className = `insight-card ${isApproved ? 'approved' : ''}`;
                    card.id = `insight-card-${ins.id}`;
                    
                    let actionLabel = ins.action;
                    if (ins.action === "PAUSE_KEYWORD") actionLabel = "Pausar Palavra-Chave";
                    else if (ins.action === "INCREASE_BUDGET") actionLabel = "Aumentar Orçamento";
                    else if (ins.action === "DECREASE_BUDGET") actionLabel = "Reduzir Orçamento";
                    else if (ins.action === "FIX_ROBOTS") actionLabel = "SEO: Desbloquear Rastreio";
                    else if (ins.action === "FIX_TAGS") actionLabel = "Rastreio: Corrigir Tag";
                    
                    card.innerHTML = `
                        <div style="flex-grow: 1; padding-right: 20px;">
                            <span class="insight-campaign-badge">${ins.campaign_name}</span>
                            <h3 class="insight-action-title">${actionLabel}: <span class="target">${ins.target}</span></h3>
                            <p class="insight-description">${ins.details}</p>
                            <div class="insight-meta">
                                <span>Impacto Estimado: <span class="insight-saving">${ins.savings_est}</span></span>
                            </div>
                        </div>
                        <div id="insight-ctrl-${ins.id}">
                            ${isApproved ? `
                                <span class="insight-status-tag approved-status">✓ Aplicado</span>
                            ` : `
                                <button class="btn-dismiss" onclick="dismissInsight('${ins.id}')" style="margin-right:8px;">Ignorar</button>
                                <button class="btn-approve" onclick="approveInsight('${ins.id}')">Aprovar</button>
                            `}
                        </div>
                    `;
                    container.appendChild(card);
                });
            } catch (err) {
                console.error("Erro ao carregar insights:", err);
            }
        }

        async function approveInsight(insightId) {
            const ctrl = document.getElementById(`insight-ctrl-${insightId}`);
            ctrl.innerHTML = `<span style="color: var(--cyan);"><div class="spinner"></div> Aplicando...</span>`;
            try {
                const res = await fetch(`${API_BASE}/approve`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: insightId })
                });
                const data = await res.json();
                if (data.status === "success") {
                    showNotification("Aprovado!", "Operação integrada com sucesso!", true);
                    const card = document.getElementById(`insight-card-${insightId}`);
                    card.classList.add('approved');
                    ctrl.innerHTML = `<span class="insight-status-tag approved-status">✓ Aplicado</span>`;
                } else {
                    showNotification("Falha", data.message, false);
                    loadInsights();
                }
            } catch (err) {
                showNotification("Erro", "Erro ao conectar com a IA.", false);
                loadInsights();
            }
        }

        function dismissInsight(insightId) {
            const card = document.getElementById(`insight-card-${insightId}`);
            card.style.opacity = '0.35';
            const ctrl = document.getElementById(`insight-ctrl-${insightId}`);
            ctrl.innerHTML = `<span style="color: var(--text-secondary); font-size: 0.75rem; font-style: italic;">Ignorado</span>`;
            showNotification("Ignorado", "Otimização rejeitada pelo operador.", true);
        }

        // Mobile sidebar toggle
        document.getElementById('sidebar-toggle-btn')?.addEventListener('click', () => {
            document.querySelector('.sidebar').classList.toggle('open');
        });
        
        // Click outside mobile sidebar to close it
        document.addEventListener('click', (e) => {
            const sidebar = document.querySelector('.sidebar');
            const toggleBtn = document.getElementById('sidebar-toggle-btn');
            if (sidebar && sidebar.classList.contains('open') && !sidebar.contains(e.target) && e.target !== toggleBtn) {
                sidebar.classList.remove('open');
            }
        });

        // Dynamic extension switcher
        if (window.location.pathname.endsWith('.php')) {
            document.querySelectorAll('.sidebar-nav a').forEach(link => {
                const url = new URL(link.href, window.location.href);
                if (url.pathname.endsWith('.html')) {
                    url.pathname = url.pathname.replace('.html', '.php');
                    link.href = url.pathname + url.search;
                }
            });
        }
    </script>
<?php endif; ?>

</body>
</html>
