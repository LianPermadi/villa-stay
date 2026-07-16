<?php

namespace App\Services;

use App\Support\XlsxWorkbook;
use Carbon\Carbon;
use Illuminate\Support\Collection;

final class BookingReportExporter
{
    private const MONTHS = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function export(Collection $payments, string $currency, Carbon $from, Carbon $to, string $path): void
    {
        $workbook = new XlsxWorkbook;
        $grouped = $payments->groupBy(fn ($payment) => $payment->created_at->format('Y-m'));

        $workbook->addSheet(
            'Ringkasan',
            $this->summaryRows($grouped, $currency, $from, $to),
            ['A' => 3, 'B' => 21, 'C' => 21, 'D' => 21, 'E' => 21],
            ['B1:E1', 'C2:E2', 'C3:E3', 'B5:E5'],
            'B7',
            'B6:E'.(7 + max(1, $grouped->count()))
        );

        foreach ($grouped as $period => $monthlyPayments) {
            $date = Carbon::createFromFormat('Y-m', $period);
            $sheetName = self::MONTHS[$date->month].' '.$date->year;
            $workbook->addSheet(
                $sheetName,
                $this->detailRows($monthlyPayments, $currency, $sheetName),
                ['A' => 2, 'B' => 13, 'C' => 18, 'D' => 24, 'E' => 22, 'F' => 18, 'G' => 17, 'H' => 3, 'I' => 13, 'J' => 18, 'K' => 24, 'L' => 22, 'M' => 18, 'N' => 17],
                ['B1:G1', 'B7:G7', 'I7:N7'],
                'B9'
            );
        }

        $workbook->save($path);
    }

    private function summaryRows(Collection $grouped, string $currency, Carbon $from, Carbon $to): array
    {
        $rows = [
            ['B' => ['value' => 'VILLA-SINA', 'style' => 1]],
            ['B' => ['value' => 'Periode', 'style' => 2], 'C' => $from->format('d/m/Y').' - '.$to->format('d/m/Y')],
            ['B' => ['value' => 'Mata Uang', 'style' => 2], 'C' => $currency],
            [],
            ['B' => ['value' => 'LAPORAN KEUANGAN', 'style' => 1]],
            [
                'B' => ['value' => 'Bulan', 'style' => 4],
                'C' => ['value' => 'Pemasukan Kotor', 'style' => 4],
                'D' => ['value' => 'Pengeluaran (Refund)', 'style' => 4],
                'E' => ['value' => 'Pendapatan Bersih', 'style' => 4],
            ],
        ];

        $excelRow = 7;
        foreach ($grouped as $period => $payments) {
            $date = Carbon::createFromFormat('Y-m', $period);
            $income = $payments->where('payment_type', '!=', 'refund')->sum(fn ($payment) => abs((float) $payment->amount));
            $expense = $payments->where('payment_type', 'refund')->sum(fn ($payment) => abs((float) $payment->amount));
            $style = $excelRow % 2 === 0 ? 10 : 6;
            $rows[] = [
                'B' => ['value' => self::MONTHS[$date->month].' '.$date->year, 'style' => $excelRow % 2 === 0 ? 9 : 5],
                'C' => ['value' => $income, 'style' => $style, 'type' => 'number'],
                'D' => ['value' => $expense, 'style' => $style, 'type' => 'number'],
                'E' => ['value' => $income - $expense, 'style' => $style, 'type' => 'number', 'formula' => 'C'.$excelRow.'-D'.$excelRow],
            ];
            $excelRow++;
        }

        if ($grouped->isEmpty()) {
            $rows[] = ['B' => ['value' => 'Tidak ada transaksi pada periode ini.', 'style' => 5]];
            $excelRow++;
        }

        $excelStart = 7;
        $excelEnd = max($excelStart, $excelRow - 1);
        $rows[] = [
            'B' => ['value' => 'TOTAL', 'style' => 8],
            'C' => ['value' => $grouped->flatten(1)->where('payment_type', '!=', 'refund')->sum(fn ($payment) => abs((float) $payment->amount)), 'style' => 7, 'type' => 'number', 'formula' => 'SUM(C'.$excelStart.':C'.$excelEnd.')'],
            'D' => ['value' => $grouped->flatten(1)->where('payment_type', 'refund')->sum(fn ($payment) => abs((float) $payment->amount)), 'style' => 7, 'type' => 'number', 'formula' => 'SUM(D'.$excelStart.':D'.$excelEnd.')'],
            'E' => ['value' => $grouped->flatten(1)->sum(fn ($payment) => (float) $payment->amount), 'style' => 7, 'type' => 'number', 'formula' => 'C'.$excelRow.'-D'.$excelRow],
        ];

        return $rows;
    }

    private function detailRows(Collection $payments, string $currency, string $period): array
    {
        $income = $payments->where('payment_type', '!=', 'refund')->values();
        $refunds = $payments->where('payment_type', 'refund')->values();
        $gross = $income->sum(fn ($payment) => abs((float) $payment->amount));
        $expense = $refunds->sum(fn ($payment) => abs((float) $payment->amount));

        $rows = [
            ['B' => ['value' => 'LAPORAN '.$period.' - '.$currency, 'style' => 1]],
            ['B' => ['value' => 'Pemasukan Kotor', 'style' => 2], 'C' => ['value' => $gross, 'style' => 6, 'type' => 'number']],
            ['B' => ['value' => 'Pengeluaran', 'style' => 2], 'C' => ['value' => $expense, 'style' => 11, 'type' => 'number']],
            ['B' => ['value' => 'Pendapatan Bersih', 'style' => 2], 'C' => ['value' => $gross - $expense, 'style' => 7, 'type' => 'number']],
            [],
            [],
            ['B' => ['value' => 'PENGELUARAN / REFUND', 'style' => 3], 'I' => ['value' => 'PEMASUKAN', 'style' => 3]],
            $this->detailHeaders(),
        ];

        $count = max($income->count(), $refunds->count(), 1);
        for ($index = 0; $index < $count; $index++) {
            $rowStyle = $index % 2 === 0 ? 5 : 9;
            $rows[] = array_merge(
                $this->paymentCells($refunds->get($index), 'B', $rowStyle, true),
                $this->paymentCells($income->get($index), 'I', $rowStyle, false)
            );
        }

        $rows[] = [
            'B' => ['value' => 'TOTAL REFUND', 'style' => 8],
            'G' => ['value' => $expense, 'style' => 7, 'type' => 'number'],
            'I' => ['value' => 'TOTAL PEMASUKAN', 'style' => 8],
            'N' => ['value' => $gross, 'style' => 7, 'type' => 'number'],
        ];

        return $rows;
    }

    private function detailHeaders(): array
    {
        $headers = ['Tanggal', 'Booking', 'Villa', 'Pelanggan', 'Metode', 'Jumlah'];
        $row = [];
        foreach (['B', 'C', 'D', 'E', 'F', 'G'] as $index => $column) {
            $row[$column] = ['value' => $headers[$index], 'style' => 4];
        }
        foreach (['I', 'J', 'K', 'L', 'M', 'N'] as $index => $column) {
            $row[$column] = ['value' => $headers[$index], 'style' => 4];
        }

        return $row;
    }

    private function paymentCells($payment, string $startColumn, int $style, bool $isRefund): array
    {
        if (! $payment) {
            return [];
        }

        $columns = $startColumn === 'B' ? ['B', 'C', 'D', 'E', 'F', 'G'] : ['I', 'J', 'K', 'L', 'M', 'N'];
        $booking = $payment->booking;
        $values = [
            $payment->created_at->format('d/m/Y'),
            '#'.$booking->id,
            $booking->villa?->name ?? '-',
            $booking->user?->name ?? '-',
            ucfirst(str_replace('_', ' ', $payment->payment_method)),
            abs((float) $payment->amount),
        ];
        $cells = [];
        foreach ($columns as $index => $column) {
            $cells[$column] = [
                'value' => $values[$index],
                'style' => $index === 5 ? ($isRefund ? 11 : ($style === 9 ? 10 : 6)) : $style,
                'type' => $index === 5 ? 'number' : 'string',
            ];
        }

        return $cells;
    }
}
