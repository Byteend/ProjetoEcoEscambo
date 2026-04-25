// ── Dados simulados (30 produtos = 3 páginas de 10) ──
const produtos = Array.from({ length: 30 }, (_, i) => ({
  id: i + 1,
  titulo: 'Título do Produto',
  descricao: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
  interesse: i === 1, // produto 2 já marcado como interessado
}));

const POR_PAGINA = 10;
let paginaAtual = 1;
let filtroAtivo = 'todos'; // 'todos' | 'interessados'

// ── SVG placeholder de imagem ──
const imgPlaceholder = `
  <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
    <rect x="2" y="2" width="44" height="44" rx="4" stroke="#4A7C59" stroke-width="2"/>
    <line x1="2" y1="2" x2="46" y2="46" stroke="#4A7C59" stroke-width="2"/>
    <line x1="46" y1="2" x2="2" y2="46" stroke="#4A7C59" stroke-width="2"/>
  </svg>`;

// ── Renderiza lista de produtos ──
function renderProdutos() {
  const lista = document.getElementById('productList');
  lista.innerHTML = '';

  const filtrados = filtroAtivo === 'interessados'
    ? produtos.filter(p => p.interesse)
    : produtos;

  const totalPaginas = Math.ceil(filtrados.length / POR_PAGINA);
  if (paginaAtual > totalPaginas) paginaAtual = 1;

  const inicio = (paginaAtual - 1) * POR_PAGINA;
  const pagina = filtrados.slice(inicio, inicio + POR_PAGINA);

  if (pagina.length === 0) {
    lista.innerHTML = '<li style="color:var(--text-muted);text-align:center;padding:2rem;">Nenhum produto encontrado.</li>';
  }

  pagina.forEach(produto => {
    const li = document.createElement('li');
    li.className = 'product-card';
    li.innerHTML = `
      <div class="product-img">${imgPlaceholder}</div>
      <div class="product-info">
        <div class="product-title">${produto.titulo}</div>
        <div class="product-desc">${produto.descricao}</div>
      </div>
      <button
        class="btn ${produto.interesse ? 'btn-remove' : 'btn-interest'}"
        data-id="${produto.id}"
        aria-label="${produto.interesse ? 'Tirar interesse do produto ' + produto.id : 'Marcar interesse no produto ' + produto.id}"
      >
        ${produto.interesse ? 'Tirar Interesse' : 'Tenho interesse'}
      </button>
    `;
    lista.appendChild(li);
  });

  renderPaginacao(totalPaginas);
}

// ── Renderiza paginação ──
function renderPaginacao(total) {
  const pag = document.getElementById('pagination');
  pag.innerHTML = '';

  if (total <= 1) return;

  const prev = criarBotaoPag('<< Prev', 'nav-page', paginaAtual === 1);
  prev.addEventListener('click', () => { if (paginaAtual > 1) { paginaAtual--; renderProdutos(); } });
  pag.appendChild(prev);

  for (let i = 1; i <= total; i++) {
    const btn = criarBotaoPag(String(i), i === paginaAtual ? 'active' : '');
    btn.addEventListener('click', () => { paginaAtual = i; renderProdutos(); });
    pag.appendChild(btn);
  }

  const next = criarBotaoPag('Next >>', 'nav-page', paginaAtual === total);
  next.addEventListener('click', () => { if (paginaAtual < total) { paginaAtual++; renderProdutos(); } });
  pag.appendChild(next);
}

function criarBotaoPag(texto, extraClass = '', desabilitado = false) {
  const btn = document.createElement('button');
  btn.className = `page-btn ${extraClass}`;
  btn.textContent = texto;
  if (desabilitado) { btn.style.opacity = '.4'; btn.style.cursor = 'default'; }
  return btn;
}

// ── Toggle de interesse ──
document.getElementById('productList').addEventListener('click', e => {
  const btn = e.target.closest('button[data-id]');
  if (!btn) return;

  const id = Number(btn.dataset.id);
  const produto = produtos.find(p => p.id === id);
  if (!produto) return;

  produto.interesse = !produto.interesse;
  renderProdutos();
});

// ── Filtro ──
document.querySelectorAll('input[name="filtro"]').forEach(radio => {
  radio.addEventListener('change', e => {
    filtroAtivo = e.target.value;
    paginaAtual = 1;
    renderProdutos();
  });
});

// ── Init ──
renderProdutos();
