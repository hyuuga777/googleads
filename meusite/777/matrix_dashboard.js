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
