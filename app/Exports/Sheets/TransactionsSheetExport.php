<?php

namespace App\Exports\Sheets;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class TransactionsSheetExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $date;

    public function __construct(string $date)
    {
        $this->date = $date;
    }

    public function collection()
    {
        return Transaction::with(['product', 'supplier'])
            ->whereDate('transacted_at', $this->date)
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Produk',
            'Nama Supplier',
            'Reference',
            'Transacted At',
            'Buyer Name',
            'Quantity',
            'Unit Price',
            'Unit Profit',
            'Total Price',
            'Debt Amount',
            'Change Due',
            'Status',
            'Note',
        ];
    }

    /**
     * @param Transaction $transaction
     */
    public function map($transaction): array
    {
        return [
            $transaction->id,
            $transaction->product ? $transaction->product->name : '',
            $transaction->supplier ? $transaction->supplier->name : '',
            $transaction->reference,
            $transaction->transacted_at,
            $transaction->buyer_name,
            $transaction->quantity,
            $transaction->unit_price,
            $transaction->unit_profit,
            $transaction->total_price,
            $transaction->debt_amount,
            $transaction->change_due,
            $transaction->status,
            $transaction->note,
        ];
    }

    public function title(): string
    {
        return 'Transactions';
    }
}
