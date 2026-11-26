<?php
/**
 * Gerador PDF simples, sem biblioteca externa.
 * É usado pelos relatórios do Arboriza.
 */
class SimplePdf
{
    private string $title;
    private array $rows = [];

    public function __construct(string $title = '')
    {
        $this->title = $title;
    }

    public function table(array $headers, array $rows): void
    {
        $this->rows[] = ['type' => 'table', 'headers' => $headers, 'rows' => $rows];
    }

    public function output(string $filename = 'relatorio.pdf'): void
    {
        $filename = preg_replace('/[^A-Za-z0-9._-]/', '_', $filename) ?: 'relatorio.pdf';
        if (substr(strtolower($filename), -4) !== '.pdf') {
            $filename .= '.pdf';
        }

        // Calcula largura máxima de cada coluna para alinhar a tabela
        $colWidths = [];
        $maxLineLen = 60;
        foreach ($this->rows as $block) {
            foreach ($block['headers'] as $i => $h) {
                $len = mb_strlen($this->clean($h), 'UTF-8');
                $colWidths[$i] = max($colWidths[$i] ?? 0, $len);
            }
            foreach ($block['rows'] as $row) {
                foreach ($row as $i => $cell) {
                    $len = mb_strlen($this->clean($cell), 'UTF-8');
                    $colWidths[$i] = max($colWidths[$i] ?? 0, $len);
                }
            }
            $totalWidth = array_sum($colWidths) + (max(0, count($colWidths) - 1) * 3);
            $maxLineLen = max($maxLineLen, $totalWidth);
        }

        // Ajusta o tamanho da fonte para caber na largura A4 (595pt com margens de 40pt)
        $fontSize = 10;
        if ($maxLineLen > 85) {
            $fontSize = 8;
        } elseif ($maxLineLen > 72) {
            $fontSize = 9;
        }
        $leading = $fontSize + 5;

        $ops = [];
        $y = 800;

        // Título em negrito
        $ops[] = "BT /F2 15 Tf 40 {$y} Td (" . $this->pdfText($this->title) . ") Tj ET";
        $y -= 18;

        // Subtítulo com data de emissão
        $ops[] = "BT /F1 9 Tf 40 {$y} Td (" . $this->pdfText('Emitido em: ' . date('d/m/Y H:i')) . ") Tj ET";
        $y -= 24;

        foreach ($this->rows as $block) {
            if (empty($block['rows'])) {
                $ops[] = "BT /F1 {$fontSize} Tf 40 {$y} Td (" . $this->pdfText('Nenhum registro encontrado.') . ") Tj ET";
                $y -= $leading;
                continue;
            }

            // Cabeçalho da tabela em negrito
            $headerText = $this->formatRow($block['headers'], $colWidths);
            $ops[] = "BT /F2 {$fontSize} Tf 40 {$y} Td (" . $this->pdfText($headerText) . ") Tj ET";
            $y -= $leading;

            // Linha divisória sob medida
            $divider = $this->makeDivider($colWidths);
            $ops[] = "BT /F1 {$fontSize} Tf 40 {$y} Td (" . $this->pdfText($divider) . ") Tj ET";
            $y -= $leading;

            // Linhas de dados
            foreach ($block['rows'] as $row) {
                $lineText = $this->formatRow($row, $colWidths);
                $ops[] = "BT /F1 {$fontSize} Tf 40 {$y} Td (" . $this->pdfText($lineText) . ") Tj ET";
                $y -= $leading;
            }
            $y -= 10;
        }

        $content = implode("\n", $ops) . "\n";

        $objects = [];
        $objects[] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objects[] = '<< /Type /Pages /Kids [3 0 R] /Count 1 >>';
        $objects[] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 5 0 R /F2 6 0 R >> >> /Contents 4 0 R >>';
        $objects[] = '<< /Length ' . strlen($content) . " >>\nstream\n" . $content . 'endstream';
        $objects[] = '<< /Type /Font /Subtype /Type1 /BaseFont /Courier /Encoding /WinAnsiEncoding >>';
        $objects[] = '<< /Type /Font /Subtype /Type1 /BaseFont /Courier-Bold /Encoding /WinAnsiEncoding >>';

        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [0];
        foreach ($objects as $i => $object) {
            $num = $i + 1;
            $offsets[$num] = strlen($pdf);
            $pdf .= $num . " 0 obj\n" . $object . "\nendobj\n";
        }

        $xref = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n" . $xref . "\n%%EOF";

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pdf));
        echo $pdf;
        exit;
    }

    private function formatRow(array $cells, array $colWidths): string
    {
        $parts = [];
        foreach ($colWidths as $i => $width) {
            $text = $this->clean($cells[$i] ?? '');
            $len = mb_strlen($text, 'UTF-8');
            $pad = max(0, $width - $len);
            // Alinha números à direita e textos à esquerda
            if (is_numeric(str_replace([',', '.'], '', $text)) && $text !== '') {
                $parts[] = str_repeat(' ', $pad) . $text;
            } else {
                $parts[] = $text . str_repeat(' ', $pad);
            }
        }
        return implode(' | ', $parts);
    }

    private function makeDivider(array $colWidths): string
    {
        $parts = [];
        foreach ($colWidths as $width) {
            $parts[] = str_repeat('-', $width);
        }
        return implode('-+-', $parts);
    }

    private function clean($value): string
    {
        $value = (string)$value;
        return str_replace(["\r", "\n", '|'], [' ', ' ', '/'], $value);
    }

    private function pdfText(string $text): string
    {
        $converted = @iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $text);
        if ($converted !== false) {
            $text = $converted;
        }
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }
}