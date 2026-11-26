document.addEventListener('DOMContentLoaded', function () {
    const conservacaoColors = {
        'Excelente': '#2E7D32',
        'Bom': '#7A9A2B',
        'Regular': '#C58A12',
        'Crítico': '#B3261E'
    };

    if (typeof window.Chart !== 'function') {
        document.querySelectorAll('.chart-wrap').forEach(function (wrap) {
            wrap.innerHTML = '<p class="empty-state">Chart.js local não encontrado em <code>assets/vendor/chart.js</code>.</p>';
        });
        return;
    }

    Chart.defaults.font.family = "'Public Sans', system-ui, sans-serif";
    Chart.defaults.color = '#16231D';

    fetch('api/dashboard_stats.php')
        .then(function (response) { return response.json(); })
        .then(function (data) {
            const ctxBairros = document.getElementById('chartBairros');
            if (ctxBairros) {
                new Chart(ctxBairros, {
                    type: 'bar',
                    data: {
                        labels: data.bairros.labels,
                        datasets: [{
                            data: data.bairros.values,
                            backgroundColor: '#2F6B4F',
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                    }
                });
            }

            const ctxConservacao = document.getElementById('chartConservacao');
            if (ctxConservacao) {
                new Chart(ctxConservacao, {
                    type: 'bar',
                    data: {
                        labels: data.conservacao.labels,
                        datasets: [{
                            data: data.conservacao.values,
                            backgroundColor: data.conservacao.labels.map(function (l) {
                                return conservacaoColors[l] || '#5B6B63';
                            }),
                            borderRadius: 4
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { x: { beginAtZero: true, ticks: { precision: 0 } } }
                    }
                });
            }

            const ctxSolicitacoes = document.getElementById('chartSolicitacoes');
            if (ctxSolicitacoes) {
                new Chart(ctxSolicitacoes, {
                    type: 'bar',
                    data: {
                        labels: data.solicitacoes.labels,
                        datasets: [{
                            data: data.solicitacoes.values,
                            backgroundColor: '#2F6B4F',
                            borderRadius: 4
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { x: { beginAtZero: true, ticks: { precision: 0 } } }
                    }
                });
            }
        });
});
