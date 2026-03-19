<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Smalot\PdfParser\Parser as PdfParser;

class FileExtractService
{
    public function extract(\Illuminate\Http\UploadedFile $file): array
    {
        $ext = strtolower($file->getClientOriginalExtension());
        return match ($ext) {
            'pdf'   => $this->fromPdf($file->getRealPath()),
            'xlsx', 'xls', 'csv' => $this->fromSpreadsheet($file->getRealPath(), $ext),
            'txt'   => ['text' => file_get_contents($file->getRealPath()), 'rows' => []],
            // Optional: add 'docx' using PhpWord or XML read
            default => ['text' => file_get_contents($file->getRealPath()), 'rows' => []],
        };
    }

    private function fromPdf(string $path): array
    {
        $parser = new PdfParser();
        $pdf    = $parser->parseFile($path);
        $text   = $pdf->getText();
        return ['text' => $text, 'rows' => []];
    }

    private function fromSpreadsheet(string $path, string $ext): array
    {
        $reader = IOFactory::createReader($ext === 'csv' ? 'Csv' : 'Xlsx');
        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = [];
        foreach ($sheet->toArray(null, true, true, true) as $r) {
            // Try to normalize columns by header names if present
            // Expect possible headers like: Item, Description, Qty, Quantity, Unit Price, Price, Rate, Total, Amount
            $rows[] = array_values($r);
        }

        // Attempt header detection and mapping
        $normalized = $this->normalizeItemRows($rows);

        return [
            'text' => '',         // we rely on structured rows here
            'rows' => $normalized // normalized array of items
        ];
    }

    private function normalizeItemRows(array $rows): array
    {
        if (empty($rows)) return [];
        // Assume first row is header if it contains any keyword
        $headers = array_map('strval', $rows[0]);
        $hasHeader = $this->looksLikeHeader($headers);
        $data = $hasHeader ? array_slice($rows, 1) : $rows;

        // Build a map for common columns
        $idx = [
            'item' => $this->findCol($headers, ['item', 'code', 'item code', 'part', 'sku', 'description']),
            'desc' => $this->findCol($headers, ['description', 'item description', 'details']),
            'qty'  => $this->findCol($headers, ['qty', 'quantity']),
            'price'=> $this->findCol($headers, ['unit price', 'price', 'rate', 'unit rate']),
            'total'=> $this->findCol($headers, ['total', 'amount', 'line total']),
        ];

        $out = [];
        foreach ($data as $row) {
            $get = fn($key) => isset($idx[$key]) && $idx[$key] !== null ? $row[$idx[$key]] ?? null : null;

            $qty   = $this->toNumber($get('qty'));
            $price = $this->toNumber($get('price'));
            $total = $this->toNumber($get('total'));

            $out[] = [
                'item_ref'        => trim((string) ($get('item') ?? $get('desc') ?? '')),
                'description'     => trim((string) ($get('desc') ?? $get('item') ?? '')),
                'qty'             => $qty ?? 0,
                'unit_price'      => $price ?? 0,
                'line_total'      => $total ?? (($qty !== null && $price !== null) ? $qty * $price : 0),
            ];
        }
        // Filter empty lines
        return array_values(array_filter($out, fn($r) => $r['item_ref'] !== '' || $r['qty'] > 0 || $r['unit_price'] > 0));
    }

    private function looksLikeHeader(array $headers): bool
    {
        $joined = strtolower(implode(' ', $headers));
        return str_contains($joined, 'qty') || str_contains($joined, 'quantity') ||
               str_contains($joined, 'unit') || str_contains($joined, 'price') ||
               str_contains($joined, 'total') || str_contains($joined, 'amount') ||
               str_contains($joined, 'description') || str_contains($joined, 'item');
    }

    private function findCol(array $headers, array $candidates): ?int
    {
        $headersLower = array_map(fn($h) => strtolower(trim($h)), $headers);
        foreach ($candidates as $cand) {
            $i = array_search($cand, $headersLower, true);
            if ($i !== false) return $i;
        }
        // fuzzy contains
        foreach ($headersLower as $i => $h) {
            foreach ($candidates as $cand) {
                if (str_contains($h, $cand)) return $i;
            }
        }
        return null;
    }

    private function toNumber($v): ?float
    {
        if ($v === null) return null;
        $s = trim((string)$v);
        if ($s === '') return null;
        // remove currency symbols & commas
        $s = preg_replace('/[^\d\.\-]/', '', $s);
        return is_numeric($s) ? (float) $s : null;
        }
}
