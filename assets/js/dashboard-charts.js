/**
 * Gráficos nativos em Canvas para o Dashboard.
 * Arboriza Botucatu - ADR-003, ADR-004 e ADR-006
 */

(function () {
    'use strict';

    const COLOR_ACTION = '#2F6B4F';
    const COLOR_TEXT = '#16231D';
    const COLOR_TEXT_MUTED = '#5B6B63';
    const COLOR_BORDER = '#DDE3DF';
    const FONT_BASE = '13px "Public Sans", system-ui, sans-serif';
    const FONT_TITLE = 'bold 14px "Public Sans", system-ui, sans-serif';
    const FONT_VALUE = 'bold 12px "Public Sans", system-ui, sans-serif';

    // Mapeamento semântico estrito de cores para o estado de conservação
    const CORES_CONSERVACAO = {
        'Excelente': '#2E7D32',
        'Bom':       '#557E1E',
        'Regular':   '#C58A12',
        'Crítico':   '#B3261E'
    };

    function initCanvas(canvasId, height) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return null;

        const ctx = canvas.getContext('2d');
        const ratio = window.devicePixelRatio || 1;
        const width = canvas.parentElement.clientWidth - 32; // desconta padding

        canvas.width = width * ratio;
        canvas.height = height * ratio;
        canvas.style.width = width + 'px';
        canvas.style.height = height + 'px';

        ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
        ctx.clearRect(0, 0, width, height);

        return { ctx, width, height };
    }

    /**
     * Gráfico de barras verticais (usado em Árvores por Bairro e Estado de Conservação)
     */
    function drawVerticalBarChart(canvasId, labels, values, colorProvider) {
        const h = 260;
        const setup = initCanvas(canvasId, h);
        if (!setup) return;
        const { ctx, width, height } = setup;

        const padLeft = 30;
        const padRight = 20;
        const padTop = 30;
        const padBottom = 55;
        const chartHeight = height - padTop - padBottom;
        const maxVal = Math.max(1, ...values.map(Number));
        const count = Math.max(1, labels.length);
        const slot = (width - padLeft - padRight) / count;
        const barWidth = Math.max(18, Math.min(54, slot * 0.65));

        // Linha base
        ctx.strokeStyle = COLOR_BORDER;
        ctx.lineWidth = 1;
        ctx.beginPath();
        ctx.moveTo(padLeft, height - padBottom);
        ctx.lineTo(width - padRight, height - padBottom);
        ctx.stroke();

        labels.forEach((label, i) => {
            const val = Number(values[i]) || 0;
            const barH = val > 0 ? Math.max(8, (val / maxVal) * chartHeight) : 0;
            const x = padLeft + (i * slot) + ((slot - barWidth) / 2);
            const y = height - padBottom - barH;
            const centerX = x + (barWidth / 2);

            // Cor da barra
            ctx.fillStyle = typeof colorProvider === 'function' ? colorProvider(label, i) : colorProvider;
            ctx.fillRect(x, y, barWidth, barH);

            // Valor acima da barra
            ctx.fillStyle = COLOR_TEXT;
            ctx.font = FONT_VALUE;
            ctx.textAlign = 'center';
            ctx.fillText(String(val), centerX, y - 6);

            // Rótulo abaixo da barra
            ctx.fillStyle = COLOR_TEXT_MUTED;
            ctx.font = FONT_BASE;
            ctx.textAlign = 'center';

            // Trunca se necessário
            let labelText = label;
            if (ctx.measureText(labelText).width > slot - 4) {
                while (labelText.length > 3 && ctx.measureText(labelText + '...').width > slot - 4) {
                    labelText = labelText.slice(0, -1);
                }
                labelText += '...';
            }
            ctx.fillText(labelText, centerX, height - padBottom + 18);
        });
    }

    /**
     * Gráfico de barras horizontais (usado em Solicitações por Status)
     */
    function drawHorizontalBarChart(canvasId, labels, values, barColor) {
        const h = 260;
        const setup = initCanvas(canvasId, h);
        if (!setup) return;
        const { ctx, width, height } = setup;

        const padLeft = 100;
        const padRight = 40;
        const padTop = 20;
        const padBottom = 20;
        const chartWidth = width - padLeft - padRight;
        const count = Math.max(1, labels.length);
        const slot = (height - padTop - padBottom) / count;
        const barHeight = Math.max(12, Math.min(26, slot * 0.55));
        const maxVal = Math.max(1, ...values.map(Number));

        labels.forEach((label, i) => {
            const val = Number(values[i]) || 0;
            const barW = val > 0 ? Math.max(6, (val / maxVal) * chartWidth) : 0;
            const y = padTop + (i * slot) + ((slot - barHeight) / 2);

            // Rótulo à esquerda
            ctx.fillStyle = COLOR_TEXT;
            ctx.font = FONT_BASE;
            ctx.textAlign = 'right';
            ctx.fillText(label, padLeft - 12, y + (barHeight / 2) + 4);

            // Barra
            ctx.fillStyle = barColor;
            ctx.fillRect(padLeft, y, barW, barHeight);

            // Valor à direita da barra
            ctx.fillStyle = COLOR_TEXT_MUTED;
            ctx.font = FONT_VALUE;
            ctx.textAlign = 'left';
            ctx.fillText(String(val), padLeft + barW + 8, y + (barHeight / 2) + 4);
        });
    }

    function renderCharts() {
        fetch('api/dashboard_stats.php')
            .then(res => res.json())
            .then(data => {
                // 1. Árvores por bairro (barras monocromáticas institucionais)
                if (data.bairros) {
                    drawVerticalBarChart('chartBairros', data.bairros.labels, data.bairros.values, COLOR_ACTION);
                }

                // 2. Estado de conservação (cores semânticas estritas)
                if (data.conservacao) {
                    drawVerticalBarChart('chartConservacao', data.conservacao.labels, data.conservacao.values, label => {
                        return CORES_CONSERVACAO[label] || COLOR_ACTION;
                    });
                }

                // 3. Solicitações por status (barras horizontais monocromáticas)
                if (data.solicitacoes) {
                    drawHorizontalBarChart('chartSolicitacoes', data.solicitacoes.labels, data.solicitacoes.values, COLOR_ACTION);
                }
            })
            .catch(err => {
                console.error('Falha ao carregar estatísticas do dashboard:', err);
            });
    }

    document.addEventListener('DOMContentLoaded', renderCharts);

    let resizeTimer;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(renderCharts, 150);
    });
})();
