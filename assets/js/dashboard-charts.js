function getMax(values) {
    return Math.max(1, ...values.map(Number));
}

function palette(index) {
    const colors = ['#1f8a54', '#2fb56f', '#f1c84c', '#2274a5', '#9b5de5', '#ef476f', '#06d6a0'];
    return colors[index % colors.length];
}

function roundRect(ctx, x, y, width, height, radius) {
    const r = Math.min(radius, width / 2, Math.abs(height) / 2);
    ctx.beginPath();
    ctx.moveTo(x + r, y);
    ctx.lineTo(x + width - r, y);
    ctx.quadraticCurveTo(x + width, y, x + width, y + r);
    ctx.lineTo(x + width, y + height - r);
    ctx.quadraticCurveTo(x + width, y + height, x + width - r, y + height);
    ctx.lineTo(x + r, y + height);
    ctx.quadraticCurveTo(x, y + height, x, y + height - r);
    ctx.lineTo(x, y + r);
    ctx.quadraticCurveTo(x, y, x + r, y);
    ctx.closePath();
}

function fitText(ctx, text, maxWidth) {
    text = String(text || '');
    if (ctx.measureText(text).width <= maxWidth) return text;

    while (text.length > 3 && ctx.measureText(text + '...').width > maxWidth) {
        text = text.slice(0, -1);
    }

    return text + '...';
}

function drawWrappedLabel(ctx, label, centerX, startY, maxWidth) {
    const words = String(label || '').split(' ');
    const lines = [];
    let current = '';

    words.forEach(function (word) {
        const test = current ? current + ' ' + word : word;
        if (ctx.measureText(test).width <= maxWidth) {
            current = test;
        } else {
            if (current) lines.push(current);
            current = word;
        }
    });

    if (current) lines.push(current);
    const finalLines = lines.slice(0, 2);

    if (lines.length > 2) {
        finalLines[1] = fitText(ctx, finalLines[1], maxWidth);
    }

    finalLines.forEach(function (line, idx) {
        ctx.fillText(fitText(ctx, line, maxWidth), centerX, startY + (idx * 14));
    });
}

function drawBarChart(canvasId, labels, values, title) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    const ratio = window.devicePixelRatio || 1;
    const width = canvas.clientWidth;
    const height = 340;

    canvas.width = width * ratio;
    canvas.height = height * ratio;
    ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
    ctx.clearRect(0, 0, width, height);

    const paddingLeft = 24;
    const paddingRight = 24;
    const top = 54;
    const bottom = 78;
    const chartHeight = height - top - bottom;
    const max = getMax(values);
    const count = Math.max(1, labels.length);
    const slot = (width - paddingLeft - paddingRight) / count;
    const barWidth = Math.max(28, Math.min(90, slot * 0.68));

    ctx.fillStyle = '#0f2f22';
    ctx.font = 'bold 16px Arial';
    ctx.textAlign = 'left';
    ctx.fillText(title, 14, 24);

    ctx.strokeStyle = '#edf3ef';
    ctx.lineWidth = 1;
    ctx.beginPath();
    ctx.moveTo(paddingLeft, height - bottom + 8);
    ctx.lineTo(width - paddingRight, height - bottom + 8);
    ctx.stroke();

    labels.forEach(function (label, i) {
        const value = Number(values[i]);
        const barHeight = Math.max(value > 0 ? 14 : 0, chartHeight * value / max);
        const x = paddingLeft + (i * slot) + ((slot - barWidth) / 2);
        const y = height - bottom - barHeight;
        const centerX = x + barWidth / 2;

        ctx.fillStyle = palette(i);
        roundRect(ctx, x, y, barWidth, barHeight, 10);
        ctx.fill();

        ctx.fillStyle = '#243126';
        ctx.font = 'bold 13px Arial';
        ctx.textAlign = 'center';
        ctx.fillText(value, centerX, y - 9);

        ctx.fillStyle = '#47534a';
        ctx.font = '11px Arial';
        drawWrappedLabel(ctx, label, centerX, height - 48, Math.max(48, slot - 8));
    });
}

function drawDonutChart(canvasId, labels, values, title) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    const ratio = window.devicePixelRatio || 1;
    const width = canvas.clientWidth;
    const height = 340;

    canvas.width = width * ratio;
    canvas.height = height * ratio;
    ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
    ctx.clearRect(0, 0, width, height);

    const total = values.reduce(function (sum, val) { return sum + Number(val); }, 0) || 1;
    let angle = -Math.PI / 2;
    const centerX = Math.min(width * 0.42, 210);
    const centerY = 165;

    ctx.fillStyle = '#0f2f22';
    ctx.font = 'bold 16px Arial';
    ctx.textAlign = 'left';
    ctx.fillText(title, 14, 24);

    values.forEach(function (value, i) {
        const slice = (Number(value) / total) * Math.PI * 2;
        ctx.beginPath();
        ctx.moveTo(centerX, centerY);
        ctx.arc(centerX, centerY, 82, angle, angle + slice);
        ctx.closePath();
        ctx.fillStyle = palette(i);
        ctx.fill();
        angle += slice;
    });

    ctx.beginPath();
    ctx.arc(centerX, centerY, 48, 0, Math.PI * 2);
    ctx.fillStyle = '#ffffff';
    ctx.fill();

    ctx.fillStyle = '#243126';
    ctx.font = 'bold 22px Arial';
    ctx.textAlign = 'center';
    ctx.fillText(total, centerX, centerY + 8);

    const legendX = width > 520 ? centerX + 120 : 18;
    const legendY = width > 520 ? 88 : 265;
    ctx.textAlign = 'left';

    labels.forEach(function (label, i) {
        const y = legendY + i * 20;
        ctx.fillStyle = palette(i);
        roundRect(ctx, legendX, y - 10, 11, 11, 3);
        ctx.fill();
        ctx.fillStyle = '#243126';
        ctx.font = '12px Arial';
        ctx.fillText(fitText(ctx, label + ': ' + values[i], width - legendX - 25), legendX + 18, y);
    });
}

fetch('api/dashboard_stats.php')
    .then(function (response) { return response.json(); })
    .then(function (data) {
        drawBarChart('chartBairros', data.bairros.labels, data.bairros.values, 'Árvores por bairro');
        drawDonutChart('chartEspecies', data.especies.labels, data.especies.values, 'Espécies predominantes');
        drawBarChart('chartConservacao', data.conservacao.labels, data.conservacao.values, 'Estado de conservação');
        drawDonutChart('chartSolicitacoes', data.solicitacoes.labels, data.solicitacoes.values, 'Solicitações por status');
    });

window.addEventListener('resize', function () {
    clearTimeout(window.__dashboardResizeTimer);
    window.__dashboardResizeTimer = setTimeout(function () {
        fetch('api/dashboard_stats.php')
            .then(function (response) { return response.json(); })
            .then(function (data) {
                drawBarChart('chartBairros', data.bairros.labels, data.bairros.values, 'Árvores por bairro');
                drawDonutChart('chartEspecies', data.especies.labels, data.especies.values, 'Espécies predominantes');
                drawBarChart('chartConservacao', data.conservacao.labels, data.conservacao.values, 'Estado de conservação');
                drawDonutChart('chartSolicitacoes', data.solicitacoes.labels, data.solicitacoes.values, 'Solicitações por status');
            });
    }, 200);
});
