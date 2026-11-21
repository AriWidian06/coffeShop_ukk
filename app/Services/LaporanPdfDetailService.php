<?php

namespace App\Services;

use Dompdf\Adapter\CPDF;
use Dompdf\Dompdf;
use Dompdf\FontMetrics;

/**
 * Menggambar lampiran "Rincian Transaksi" langsung pada canvas PDF.
 *
 * Tabel transaksi sebelumnya dirender sebagai HTML oleh Dompdf. Pola itu
 * membutuhkan memori sangat besar karena setiap sel menyimpan frame layout
 * sendiri: 1.000 transaksi (± 10.000 sel) menghabiskan ± 478 MB dan gagal
 * pada memory_limit 128 MB. Dengan menggambar baris satu per satu ke canvas,
 * seluruh transaksi tetap tampil lengkap dengan memori yang jauh lebih kecil.
 */
class LaporanPdfDetailService
{
    private const MARGIN_X = 21.0;          // 28px

    private const CONTENT_TOP = 66.0;       // 88px

    private const CONTENT_BOTTOM = 551.0;   // tinggi A4 landscape - footer

    private const ROW_HEIGHT = 13.0;

    private const TABLE_HEADER_HEIGHT = 15.0;

    private const HEADER_BOTTOM = 61.0;

    private const COLUMN_WEIGHTS = [4, 12, 6, 12, 15, 5, 10, 12, 13, 11];

    private const COLORS = [
        'dark' => [0.059, 0.090, 0.165],
        'muted' => [0.392, 0.455, 0.545],
        'soft' => [0.580, 0.639, 0.722],
        'blue' => [0.145, 0.388, 0.922],
        'blueDark' => [0.114, 0.306, 0.847],
        'blueSoft' => [0.937, 0.965, 1.0],
        'blueBorder' => [0.749, 0.859, 0.996],
        'border' => [0.886, 0.910, 0.941],
        'rowBorder' => [0.945, 0.961, 0.976],
        'rowAlt' => [0.973, 0.980, 0.988],
    ];

    private CPDF $canvas;

    private FontMetrics $metrics;

    private string $font;

    private string $fontBold;

    private float $pageWidth;

    private float $pageHeight;

    private float $contentWidth;

    /** @var array<int, array{x: float, width: float}> */
    private array $columns = [];

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @param  array<string, mixed>  $meta
     */
    public function append(Dompdf $dompdf, array $rows, array $meta): void
    {
        $this->canvas = $dompdf->getCanvas();
        $this->metrics = $dompdf->getFontMetrics();
        $this->font = $this->metrics->getFont('DejaVu Sans', 'normal');
        $this->fontBold = $this->metrics->getFont('DejaVu Sans', 'bold');
        $this->pageWidth = $this->canvas->get_width();
        $this->pageHeight = $this->canvas->get_height();
        $this->contentWidth = $this->pageWidth - (self::MARGIN_X * 2);
        $this->columns = $this->buildColumns();

        $summaryPages = $this->canvas->get_page_count();

        // Halaman pertama lampiran memuat judul bagian; halaman berikutnya
        // langsung dimulai dengan header tabel.
        $this->canvas->new_page();
        $y = $this->drawDetailHeading(count($rows), $meta);
        $y = $this->drawTableHeader($y);

        if ($rows === []) {
            $this->canvas->text(
                self::MARGIN_X + 4,
                $y + 16,
                'Belum ada transaksi pada periode ini.',
                $this->font,
                6.5,
                self::COLORS['muted']
            );
        }

        foreach ($rows as $index => $row) {
            if ($y + self::ROW_HEIGHT > self::CONTENT_BOTTOM) {
                $this->canvas->new_page();
                $y = $this->drawTableHeader(self::CONTENT_TOP);
            }

            $this->drawRow($y, $index + 1, $row);
            $y += self::ROW_HEIGHT;
        }

        // Header & footer hanya untuk halaman lampiran yang baru ditambahkan.
        $this->canvas->page_script(function (int $pageNumber) use ($summaryPages, $meta): void {
            if ($pageNumber <= $summaryPages) {
                return;
            }

            $this->drawPageHeader($meta);
            $this->drawPageFooter($meta);
        });
    }

    /**
     * @return array<int, array{x: float, width: float}>
     */
    private function buildColumns(): array
    {
        $columns = [];
        $x = self::MARGIN_X;
        $totalWeight = array_sum(self::COLUMN_WEIGHTS);

        foreach (self::COLUMN_WEIGHTS as $weight) {
            $width = $this->contentWidth * $weight / $totalWeight;
            $columns[] = ['x' => $x, 'width' => $width];
            $x += $width;
        }

        return $columns;
    }

    private function drawDetailHeading(int $total, array $meta): float
    {
        $x = self::MARGIN_X;
        $y = 68.0;

        $this->canvas->text($x, $y, 'BUKU KAS & TRANSAKSI', $this->fontBold, 4.2, self::COLORS['blue']);
        $this->canvas->text($x, $y + 9, 'Rincian Transaksi', $this->fontBold, 9.2, self::COLORS['dark']);
        $this->canvas->text(
            $x,
            $y + 22,
            $this->fit(
                $total.' transaksi pada '.$meta['periodeLabel'].' • waktu disajikan dalam WIB',
                $this->font,
                4.8,
                $this->contentWidth - 90
            ),
            $this->font,
            4.8,
            self::COLORS['muted']
        );

        $label = number_format($total, 0, ',', '.').' Transaksi';
        $pillWidth = $this->metrics->getTextWidth($label, $this->fontBold, 4.6) + 12;
        $pillX = $this->pageWidth - self::MARGIN_X - $pillWidth;

        $this->canvas->filled_rectangle($pillX, $y + 1, $pillWidth, 13, self::COLORS['blueSoft']);
        $this->canvas->rectangle($pillX, $y + 1, $pillWidth, 13, self::COLORS['blueBorder'], 0.5);
        $this->canvas->text(
            $pillX + 6,
            $this->textTop($y + 1, 13, $this->fontBold, 4.6),
            $label,
            $this->fontBold,
            4.6,
            self::COLORS['blueDark']
        );

        return $y + 34;
    }

    private function drawTableHeader(float $y): float
    {
        $headings = ['No', 'Waktu (WIB)', 'Tiket', 'Meja / Tipe', 'Kasir', 'Item', 'Diskon', 'Total', 'Pembayaran', 'Status'];

        $this->canvas->filled_rectangle(
            self::MARGIN_X,
            $y,
            $this->contentWidth,
            self::TABLE_HEADER_HEIGHT,
            self::COLORS['blueSoft']
        );

        foreach ($headings as $index => $heading) {
            $column = $this->columns[$index];
            $isRight = in_array($index, [5, 6, 7], true);
            $this->drawCellText(
                $column['x'],
                $this->textTop($y, self::TABLE_HEADER_HEIGHT, $this->fontBold, 4.35),
                $column['width'],
                $heading,
                $this->fontBold,
                4.35,
                self::COLORS['blueDark'],
                $isRight
            );
        }

        $this->canvas->line(
            self::MARGIN_X,
            $y + self::TABLE_HEADER_HEIGHT,
            self::MARGIN_X + $this->contentWidth,
            $y + self::TABLE_HEADER_HEIGHT,
            self::COLORS['blueBorder'],
            0.5
        );

        return $y + self::TABLE_HEADER_HEIGHT;
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function drawRow(float $y, int $number, array $row): void
    {
        if ($number % 2 === 0) {
            $this->canvas->filled_rectangle(
                self::MARGIN_X,
                $y,
                $this->contentWidth,
                self::ROW_HEIGHT,
                self::COLORS['rowAlt']
            );
        }

        $cellY = $y + 3.0;
        $top5 = $this->textTop($y, self::ROW_HEIGHT, $this->font, 5.0);
        $top48 = $this->textTop($y, self::ROW_HEIGHT, $this->font, 4.8);
        $top46 = $this->textTop($y, self::ROW_HEIGHT, $this->font, 4.6);

        $this->drawCellText($this->columns[0]['x'], $top5, $this->columns[0]['width'], (string) $number, $this->font, 5.0, self::COLORS['soft']);
        $this->drawCellText($this->columns[1]['x'], $top5, $this->columns[1]['width'], $row['waktu'], $this->font, 5.0, self::COLORS['dark']);
        $this->drawCellText($this->columns[2]['x'], $top5, $this->columns[2]['width'], $row['tiket'], $this->fontBold, 5.0, self::COLORS['blueDark']);
        $this->drawCellText($this->columns[3]['x'], $top46, $this->columns[3]['width'], $row['meja_tipe'], $this->font, 4.6, self::COLORS['dark']);
        $this->drawCellText($this->columns[4]['x'], $top48, $this->columns[4]['width'], $row['kasir'], $this->font, 4.8, self::COLORS['dark']);
        $this->drawCellText($this->columns[5]['x'], $top5, $this->columns[5]['width'], $row['item'], $this->font, 5.0, self::COLORS['dark'], true);
        $this->drawCellText($this->columns[6]['x'], $top5, $this->columns[6]['width'], $row['diskon'], $this->font, 5.0, self::COLORS['dark'], true);
        $this->drawCellText($this->columns[7]['x'], $top5, $this->columns[7]['width'], $row['total'], $this->fontBold, 5.0, self::COLORS['dark'], true);

        $this->drawPayment($this->columns[8], $cellY, $row);
        $this->drawStatus($this->columns[9], $cellY, $row);

        $this->canvas->line(
            self::MARGIN_X,
            $y + self::ROW_HEIGHT,
            self::MARGIN_X + $this->contentWidth,
            $y + self::ROW_HEIGHT,
            self::COLORS['rowBorder'],
            0.5
        );
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function drawPayment(array $column, float $y, array $row): void
    {
        $paid = (bool) $row['lunas'];
        $label = $paid ? 'Lunas' : 'Belum Lunas';
        $background = $paid ? [0.820, 0.980, 0.898] : [0.996, 0.886, 0.886];
        $foreground = $paid ? [0.016, 0.471, 0.341] : [0.725, 0.110, 0.110];

        $badgeWidth = $this->metrics->getTextWidth($label, $this->fontBold, 4.2) + 6;
        $x = $column['x'] + 3.75;
        $badgeY = $y + 0.5;
        $textY = $this->textTop($badgeY, 9, $this->fontBold, 4.2);

        $this->canvas->filled_rectangle($x, $badgeY, $badgeWidth, 9, $background);
        $this->canvas->text($x + 3, $textY, $label, $this->fontBold, 4.2, $foreground);

        if ($paid && $row['metode'] !== '') {
            $methodX = $x + $badgeWidth + 3;
            $available = ($column['x'] + $column['width'] - 3.75) - $methodX;

            if ($available > 6) {
                $this->canvas->text(
                    $methodX,
                    $textY,
                    $this->fit($row['metode'], $this->font, 4.2, $available),
                    $this->font,
                    4.2,
                    self::COLORS['muted']
                );
            }
        }
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function drawStatus(array $column, float $y, array $row): void
    {
        $palette = [
            'pending' => [[0.996, 0.953, 0.780], [0.573, 0.251, 0.055]],
            'prosesed' => [[0.929, 0.914, 0.996], [0.357, 0.129, 0.714]],
            'ready' => [[0.859, 0.918, 0.996], [0.114, 0.306, 0.847]],
            'completed' => [[0.820, 0.980, 0.898], [0.016, 0.471, 0.341]],
        ];

        [$background, $foreground] = $palette[$row['status_key']] ?? $palette['pending'];

        $label = $this->fit($row['status_label'], $this->fontBold, 4.2, $column['width'] - 7.5);
        $badgeWidth = min($column['width'] - 7.5, $this->metrics->getTextWidth($label, $this->fontBold, 4.2) + 6);
        $x = $column['x'] + 3.75;
        $badgeY = $y + 0.5;

        $this->canvas->filled_rectangle($x, $badgeY, $badgeWidth, 9, $background);
        $this->canvas->text($x + 3, $this->textTop($badgeY, 9, $this->fontBold, 4.2), $label, $this->fontBold, 4.2, $foreground);
    }

    private function drawCellText(
        float $columnX,
        float $top,
        float $columnWidth,
        string $text,
        string $font,
        float $size,
        array $color,
        bool $alignRight = false
    ): void {
        $maxWidth = $columnWidth - 7.5;

        if ($maxWidth <= 0) {
            return;
        }

        $text = $this->fit($text, $font, $size, $maxWidth);
        $x = $columnX + 3.75;

        if ($alignRight) {
            $x = $columnX + $columnWidth - 3.75 - $this->metrics->getTextWidth($text, $font, $size);
        }

        $this->canvas->text($x, $top, $text, $font, $size, $color);
    }

    /**
     * Dompdf menempatkan y sebagai sisi atas teks (bukan baseline), sehingga
     * posisi perlu dihitung agar teks berada di tengah baris.
     */
    private function textTop(float $top, float $height, string $font, float $size): float
    {
        return $top + max(0.0, ($height - $this->metrics->getFontHeight($font, $size)) / 2);
    }

    private function drawPageHeader(array $meta): void
    {
        $x = self::MARGIN_X;

        if (! empty($meta['logoPath']) && is_file($meta['logoPath'])) {
            $this->canvas->image($meta['logoPath'], $x, 17, 33, 33);
        } else {
            $this->canvas->filled_rectangle($x, 17, 33, 33, self::COLORS['blueSoft']);
            $this->canvas->text($x + 11, 37, 'BK', $this->fontBold, 9, self::COLORS['blue']);
        }

        $this->canvas->text($x + 41, 19, 'Bagi Kopi', $this->fontBold, 12.75, self::COLORS['dark']);
        $this->canvas->text($x + 41, 35.5, 'ARTISANAL BREW • PERKOCI EATERY', $this->font, 4.35, self::COLORS['muted']);
        $this->canvas->text($x + 41, 42, 'LAPORAN PENJUALAN RESMI • WAKTU INDONESIA BARAT', $this->fontBold, 4.2, self::COLORS['blue']);

        $right = $this->pageWidth - self::MARGIN_X;
        $this->drawRightAligned($right, 18, 'LAPORAN PENJUALAN', $this->fontBold, 9.75, self::COLORS['blueDark']);
        $this->drawRightAligned($right, 31, $meta['periodeLabel'], $this->fontBold, 6.75, self::COLORS['dark']);
        $this->drawRightAligned($right, 40.5, $meta['rangeLabel'], $this->font, 4.6, self::COLORS['muted']);
        $this->drawRightAligned($right, 47, 'Dicetak '.$meta['printedLabel'].' WIB', $this->font, 4.6, self::COLORS['muted']);

        $this->canvas->line($x, self::HEADER_BOTTOM, $right, self::HEADER_BOTTOM, self::COLORS['border'], 0.5);
    }

    private function drawPageFooter(array $meta): void
    {
        $right = $this->pageWidth - self::MARGIN_X;

        $this->canvas->line(
            self::MARGIN_X,
            $this->pageHeight - 32,
            $right,
            $this->pageHeight - 32,
            self::COLORS['border'],
            0.5
        );

        $this->canvas->text(
            self::MARGIN_X,
            $this->pageHeight - 28,
            'Bagi Kopi • Laporan Penjualan • Dicetak oleh '.$meta['dicetakOleh'].' • Dokumen dihasilkan otomatis oleh sistem',
            $this->font,
            4.7,
            self::COLORS['muted']
        );
    }

    private function drawRightAligned(float $right, float $baseline, string $text, string $font, float $size, array $color): void
    {
        $text = $this->fit($text, $font, $size, $this->contentWidth * 0.42);
        $this->canvas->text(
            $right - $this->metrics->getTextWidth($text, $font, $size),
            $baseline,
            $text,
            $font,
            $size,
            $color
        );
    }

    /**
     * Potong teks yang terlalu panjang agar tidak menabrak kolom sebelah.
     */
    private function fit(string $text, string $font, float $size, float $maxWidth): string
    {
        if ($text === '' || $maxWidth <= 0) {
            return '';
        }

        if ($this->metrics->getTextWidth($text, $font, $size) <= $maxWidth) {
            return $text;
        }

        $ellipsis = '...';
        $low = 0;
        $high = mb_strlen($text);

        while ($low < $high) {
            $mid = intdiv($low + $high + 1, 2);
            $candidate = mb_substr($text, 0, $mid).$ellipsis;

            if ($this->metrics->getTextWidth($candidate, $font, $size) <= $maxWidth) {
                $low = $mid;
            } else {
                $high = $mid - 1;
            }
        }

        return mb_substr($text, 0, $low).$ellipsis;
    }
}
