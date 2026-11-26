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

        $lines = [$this->title, ''];
        foreach ($this->rows as $block) {
            $lines[] = implode(' | ', array_map([$this, 'clean'], $block['headers']));
            $lines[] = str_repeat('-', 100);
            foreach ($block['rows'] as $row) {
                $lines[] = implode(' | ', array_map([$this, 'clean'], $row));
            }
            $lines[] = '';
        }

        $content = "BT\n/F1 14 Tf\n50 800 Td\n";
        $first = true;
        foreach ($lines as $line) {
            $text = $this->pdfText($line);
            if (!$first) {
                $content .= "0 -18 Td\n";
            }
            $content .= '(' . $text . ") Tj\n";
            $first = false;
        }
        $content .= "ET\n";

        $objects = [];
        $objects[] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objects[] = '<< /Type /Pages /Kids [3 0 R] /Count 1 >>';
        $objects[] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 5 0 R >> >> /Contents 4 0 R >>';
        $objects[] = '<< /Length ' . strlen($content) . " >>\nstream\n" . $content . 'endstream';
        $objects[] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';

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
            $pdf .= sprintf('%010d 00000 n \n', $offsets[$i]);
        }
        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n" . $xref . "\n%%EOF";

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pdf));
        echo $pdf;
        exit;
    }

    private function clean($value): string
    {
        $value = (string)$value;
        $value = str_replace(["\r", "\n", '|'], [' ', ' ', '/'], $value);
        return $value;
    }

    private function pdfText(string $text): string
    {
        // Helvetica base-14 não é Unicode. Translitera acentos para evitar PDF inválido.
        $converted = @iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $text);
        if ($converted !== false) {
            $text = $converted;
        }
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }
}
