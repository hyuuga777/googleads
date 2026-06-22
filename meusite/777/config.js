// Configuração de API Cyborg Orchestrator
window.CYBORG_CONFIG = {
    // IMPORTANTE: Insira aqui a URL do seu VPS (ex: "http://SEU_IP_VPS:5000/api" ou "https://api.agenciacyborg.com/api")
    // Se deixar vazio "", o sistema tentará conectar no localhost (http://127.0.0.1:5000/api) 
    // e se o localhost estiver offline ele usará o modo de simulação interativo no navegador.
    api_url: (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? "http://127.0.0.1:5000/api" : "https://googleads.agenciacyborg.com/api"
};
