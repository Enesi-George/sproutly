<?php

namespace App\Modules\Transaction\Exports;

use App\Modules\Transaction\Models\Transaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionExport implements FromQuery, ShouldQueue, WithHeadings, WithStyles, WithColumnFormatting, WithMapping
{
    use Exportable;

    public function query()
    {
        return Transaction::query()
            ->select(
                'id',
                'amount',
                'status',
                'entry',
                'reference_id',
                'metadata',
                'created_at'
            );;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Amount (₦)',
            'Status',
            'Entry',
            'Reference ID',
            'Metadata',
            'Date'
        ];
    }

      public function map($transaction): array
    {
        return [
            $transaction->id,
            $transaction-> getFormattedAmountAttribute(),
            ucfirst($transaction->status),
            $transaction->entry,
            $transaction->reference_id,
            $transaction->metadata,
            $transaction->created_at->format('d-m-Y H:i:s'),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_DATE_DATETIME,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public static function generateFilePath(): string
    {
        return "sproutly_transactions.xlsx";
    }
}
