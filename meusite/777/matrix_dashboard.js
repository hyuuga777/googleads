// Efeito Matrix Rain - Versão Dashboard (Full Width, Otimizada e Premium)
const canvas = document.getElementById('matrix-canvas');
const ctx = canvas.getContext('2d');

// Obter configurações de cores e opacidades diretamente do CSS
const cssStyles = getComputedStyle(document.body);
let matrixColor = cssStyles.getPropertyValue('--matrix-color').trim() || '#00ff66';
let matrixGlowColor = cssStyles.getPropertyValue('--matrix-glow-color').trim() || '#ffffff';

const fontSize = 16; // Tamanho de fonte maior e mais visível no desktop
let columns = 0;
let drops = [];

// FPS controlado para estabilidade de performance e visual fluído
const TARGET_FPS = 40; // Maior FPS para maior velocidade e fluidez
const FRAME_INTERVAL = 1000 / TARGET_FPS;
let lastFrameTime = 0;

// IntersectionObserver para suspender a renderização quando o canvas estiver oculto
let isCanvasVisible = true;
const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    isCanvasVisible = entry.isIntersecting;
  });
}, { threshold: 0.01 });

if (canvas) {
  observer.observe(canvas);
}

// Função para configurar/redimensionar o canvas para ocupar a tela inteira
function resizeCanvas() {
  if (!canvas) return;
  canvas.width = window.innerWidth;
  canvas.height = window.innerHeight;

  const newColumns = Math.floor(canvas.width / fontSize);

  if (newColumns !== columns) {
    const newDrops = new Array(newColumns);
    for (let i = 0; i < newColumns; i++) {
      newDrops[i] = (i < columns) ? drops[i] : Math.floor(Math.random() * -30);
    }
    drops = newDrops;
    columns = newColumns;
  }
}

// Configuração Inicial
resizeCanvas();

// Símbolos clássicos do Matrix
const letters = ["日", "ﾊ", "ﾐ", "ﾋ", "ｰ", "ｳ", "ｼ", "ﾅ", "ﾓ", "ﾆ", "ｻ", "ﾜ", "ﾂ", "ｵ", "ﾘ", "ｱ", "ﾎ", "ﾃ", "ﾏ", "ｹ", "ﾒ", "ｴ", "ｶ", "ｷ", "ﾑ", "ﾕ", "ﾗ", "ｾ", "ﾈ", "ｽ", "ﾀ", "ﾇ", "ﾍ", ":", "・", ".", "=", "*", "+", "-", "<", ">", "¦"];

// Fundo escuro combinando com a cor de fundo do dashboard (--bg-dark: #030206)
let clearColor = "rgba(3, 2, 6, 0.08)";

// Função de animação com controle de FPS
function draw(timestamp) {
  window.requestAnimationFrame(draw);

  // Se o canvas não estiver visível, pula
  if (!isCanvasVisible || !canvas) return;

  // Controle de FPS
  if (timestamp - lastFrameTime < FRAME_INTERVAL) return;
  lastFrameTime = timestamp;

  // Pintar fundo semi-transparente para o rastro
  ctx.fillStyle = clearColor;
  ctx.fillRect(0, 0, canvas.width, canvas.height);

  // Fonte monospace para manter colunas alinhadas
  ctx.font = `bold ${fontSize}px monospace`;
  ctx.textAlign = 'center';

  // Desenhar a chuva
  for (let i = 0; i < columns; i++) {
    const text = letters[Math.floor(Math.random() * letters.length)];
    const x = Math.floor(i * fontSize + fontSize / 2);
    const y = Math.floor(drops[i] * fontSize);

    if (drops[i] >= 0) {
      // Cauda / Corpo da gota
      ctx.fillStyle = matrixColor;
      ctx.fillText(text, x, y - fontSize);

      // Cabeça brilhante (efeito premium em branco)
      ctx.fillStyle = matrixGlowColor;
      ctx.fillText(text, x, y);
    }

    // Reset da gota se passar do limite da tela
    if (drops[i] * fontSize > canvas.height && Math.random() > 0.985) {
      drops[i] = Math.floor(Math.random() * -15);
    }

    // Velocidade de descida mais rápida
    drops[i] += 0.8;
  }
}

// Iniciar animação de forma não-bloqueante
if ('requestIdleCallback' in window) {
  requestIdleCallback(() => { window.requestAnimationFrame(draw); }, { timeout: 500 });
} else {
  setTimeout(() => { window.requestAnimationFrame(draw); }, 200);
}

// Redimensionamento
window.addEventListener('resize', resizeCanvas);

// OpenAI API Key Modal Integration
document.addEventListener('DOMContentLoaded', () => {
    const API_BASE = (window.CYBORG_CONFIG && window.CYBORG_CONFIG.api_url) ? window.CYBORG_CONFIG.api_url : "http://127.0.0.1:5000/api";

    const badge = document.getElementById('openai-status-badge');
    if (!badge) return;

    badge.style.cursor = 'pointer';
    badge.addEventListener('click', () => {
        openOpenAiModal();
    });

    function openOpenAiModal() {
        let overlay = document.getElementById('openai-modal-overlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'openai-modal-overlay';
            overlay.className = 'cyber-modal-overlay';
            overlay.innerHTML = `
                <div class="cyber-modal">
                    <div class="cyber-modal-header">
                        <h3 class="cyber-modal-title">Chave de API OpenAI</h3>
                    </div>
                    <div class="cyber-modal-body">
                        <p class="cyber-modal-desc">
                            Insira sua chave de API da OpenAI (sk-...) para ativar a orquestração de IA e a geração inteligente de campanhas via GPT-4o-mini.
                        </p>
                        <div style="display: flex; flex-direction: column; gap: 8px; text-align: left; margin-bottom: 10px;">
                            <label for="openai-modal-key-input" style="font-size: 0.72rem; text-transform: uppercase; color: var(--text-secondary); font-weight: bold; letter-spacing: 0.05em;">Token da API</label>
                            <input type="password" id="openai-modal-key-input" class="form-input" style="width: 100%; box-sizing: border-box; padding: 10px; font-size: 0.85rem; background: rgba(0,0,0,0.3); border: 1px solid rgba(157,0,255,0.4); color: #fff; border-radius: 6px; outline: none; transition: border 0.3s;" placeholder="sk-proj-..." autocomplete="off">
                        </div>
                        <div id="openai-modal-status-msg" style="font-size: 0.75rem; color: #ffb300; display: none; margin-top: 15px; text-align: left; padding: 8px; background: rgba(255,179,0,0.05); border: 1px dashed rgba(255,179,0,0.25); border-radius: 4px; line-height: 1.4;"></div>
                    </div>
                    <div class="cyber-modal-buttons">
                        <button type="button" class="cyber-btn" style="background: rgba(255,255,255,0.05); color: #fff; padding: 8px 16px; font-size: 0.8rem; border: 1px solid rgba(255,255,255,0.1); border-radius: 4px; cursor: pointer; transition: all 0.3s;" id="openai-modal-close-btn">Cancelar</button>
                        <button type="button" class="cyber-btn" style="background: linear-gradient(135deg, var(--cyan), var(--purple)); color: #fff; padding: 8px 16px; font-size: 0.8rem; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; box-shadow: 0 0 10px rgba(0, 243, 255, 0.2); transition: all 0.3s;" id="openai-modal-save-btn">Salvar e Validar</button>
                    </div>
                </div>
            `;
            document.body.appendChild(overlay);

            document.getElementById('openai-modal-close-btn').addEventListener('click', closeOpenAiModal);
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) closeOpenAiModal();
            });

            document.getElementById('openai-modal-save-btn').addEventListener('click', saveOpenAiKey);
            
            // Add focus styles to input
            const inputEl = document.getElementById('openai-modal-key-input');
            inputEl.addEventListener('focus', () => {
                inputEl.style.borderColor = 'var(--cyan)';
                inputEl.style.boxShadow = '0 0 8px rgba(0, 243, 255, 0.3)';
            });
            inputEl.addEventListener('blur', () => {
                inputEl.style.borderColor = 'rgba(157,0,255,0.4)';
                inputEl.style.boxShadow = 'none';
            });
        }

        const input = document.getElementById('openai-modal-key-input');
        const statusMsg = document.getElementById('openai-modal-status-msg');
        statusMsg.style.display = 'none';
        input.value = '';

        fetch(`${API_BASE}/openai_key`)
            .then(res => res.json())
            .then(data => {
                if (data.openai_api_key) {
                    input.value = data.openai_api_key;
                }
            })
            .catch(err => {
                console.error("Erro ao carregar chave OpenAI:", err);
            });

        setTimeout(() => {
            overlay.classList.add('active');
        }, 50);
    }

    function closeOpenAiModal() {
        const overlay = document.getElementById('openai-modal-overlay');
        if (overlay) {
            overlay.classList.remove('active');
        }
    }

    function saveOpenAiKey() {
        const input = document.getElementById('openai-modal-key-input');
        const key = input.value.trim();
        const statusMsg = document.getElementById('openai-modal-status-msg');
        const saveBtn = document.getElementById('openai-modal-save-btn');

        saveBtn.disabled = true;
        saveBtn.textContent = 'Validando...';
        statusMsg.textContent = 'Iniciando teste de conectividade com a API OpenAI...';
        statusMsg.style.color = '#00f3ff';
        statusMsg.style.display = 'block';

        fetch(`${API_BASE}/openai_key`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ openai_api_key: key })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                if (data.openai_api === 'CONNECTED') {
                    statusMsg.textContent = 'Conectado com sucesso! Chave de API validada.';
                    statusMsg.style.color = '#00ff66';
                    
                    if (typeof showNotification === 'function') {
                        showNotification("API OpenAI", "Chave conectada e validada com sucesso!", true);
                    }
                    
                    if (typeof checkApiStatus === 'function') {
                        checkApiStatus();
                    }

                    setTimeout(() => {
                        closeOpenAiModal();
                    }, 1000);
                } else {
                    statusMsg.textContent = 'Erro ao validar a chave. Por favor, verifique se a chave é válida e se possui saldo.';
                    statusMsg.style.color = '#ff0055';
                    if (typeof showNotification === 'function') {
                        showNotification("Erro OpenAI", "Falha ao validar chave de API.", false);
                    }
                }
            } else {
                statusMsg.textContent = 'Falha ao salvar a chave no servidor.';
                statusMsg.style.color = '#ff0055';
            }
        })
        .catch(err => {
            console.error("Erro ao salvar chave:", err);
            statusMsg.textContent = 'Falha de comunicação com o backend.';
            statusMsg.style.color = '#ff0055';
        })
        .finally(() => {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Salvar e Validar';
        });
    }
});

