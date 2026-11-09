/*
  graficos.js
  -------------------------------------------------------
  Graficos do dashboard da empresa (biblioteca Chart.js).
  So e carregado em empresa/dashboard.html.
*/

// --- Bloco 1: Cores da paleta do projeto ---
const corPrimaria = '#0F766E';
const corTurquesa = '#14B8A6';
const corTurquesaClara = '#7DD3C8';
const corEscura = '#12343B';
const corCinza = '#64748B';

// --- Bloco 2: Grafico de barras (clientes novos por mes vindos 100% do banco) ---
const nomesMeses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
const canvasBarras = document.getElementById('grafico-clientes');
let clientesPorMes = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

if (canvasBarras && canvasBarras.dataset.valores) {
  const valoresMesBanco = canvasBarras.dataset.valores.split(',').map(Number);
  if (valoresMesBanco.length === 12) {
    clientesPorMes = valoresMesBanco;
  }
}

const graficoClientes = new Chart(canvasBarras, {
  type: 'bar',
  data: {
    labels: nomesMeses,
    datasets: [{
      label: 'Clientes novos',
      data: clientesPorMes,
      backgroundColor: corTurquesaClara,
      hoverBackgroundColor: corPrimaria,
      borderRadius: 4
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: false }
    },
    scales: {
      y: { beginAtZero: true }
    }
  }
});

// --- Bloco 3: Filtro de periodo do grafico (card e barra do topo) ---
const selectPeriodo = document.getElementById('periodo-grafico');
const selectTopoPeriodo = document.querySelector('.filtro-topo-periodo');

function aplicarPeriodoGrafico(quantidade) {
  graficoClientes.data.labels = nomesMeses.slice(-quantidade);
  graficoClientes.data.datasets[0].data = clientesPorMes.slice(-quantidade);
  graficoClientes.update();
}

if (selectPeriodo !== null) {
  selectPeriodo.addEventListener('change', function () {
    aplicarPeriodoGrafico(parseInt(selectPeriodo.value));
  });
}

if (selectTopoPeriodo !== null) {
  selectTopoPeriodo.addEventListener('change', function () {
    const qtd = parseInt(selectTopoPeriodo.value) || 12;
    if (selectPeriodo !== null && (qtd === 12 || qtd === 6)) {
      selectPeriodo.value = String(qtd);
    }
    aplicarPeriodoGrafico(qtd);
  });
}

// --- Bloco 4: Grafico de rosca (projetos por etapa vindos 100% do banco) + Filtro do topo ---
const etapas = ['Planejamento', 'Desenvolvimento', 'Em revisão', 'Aguardando aprovação', 'Concluído'];
const canvasProjetos = document.getElementById('grafico-projetos');
let projetosPorEtapa = [0, 0, 0, 0, 0];

if (canvasProjetos && canvasProjetos.dataset.valores) {
  const valoresBanco = canvasProjetos.dataset.valores.split(',').map(Number);
  if (valoresBanco.length === 5) {
    projetosPorEtapa = valoresBanco;
  }
}

const coresEtapas = [corTurquesa, corPrimaria, corTurquesaClara, '#B7E4DF', corCinza];

const graficoProjetos = new Chart(canvasProjetos, {
  type: 'doughnut',
  data: {
    labels: etapas,
    datasets: [{
      data: projetosPorEtapa,
      backgroundColor: coresEtapas,
      borderWidth: 2
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '65%',
    plugins: {
      legend: { position: 'right' }
    }
  }
});

const selectTopoProjeto = document.querySelector('.filtro-topo-projeto');
if (selectTopoProjeto !== null) {
  selectTopoProjeto.addEventListener('change', function () {
    const modo = selectTopoProjeto.value;
    if (modo === 'andamento') {
      graficoProjetos.data.labels = etapas.slice(0, 4);
      graficoProjetos.data.datasets[0].data = projetosPorEtapa.slice(0, 4);
      graficoProjetos.data.datasets[0].backgroundColor = coresEtapas.slice(0, 4);
    } else if (modo === 'concluidos') {
      graficoProjetos.data.labels = [etapas[4]];
      graficoProjetos.data.datasets[0].data = [projetosPorEtapa[4] || 1];
      graficoProjetos.data.datasets[0].backgroundColor = [coresEtapas[4]];
    } else {
      graficoProjetos.data.labels = etapas;
      graficoProjetos.data.datasets[0].data = projetosPorEtapa;
      graficoProjetos.data.datasets[0].backgroundColor = coresEtapas;
    }
    graficoProjetos.update();
  });
}
