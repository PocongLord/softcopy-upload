<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InvoicesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Invoice::select('vendor_name', 'invoice_receipt_number', 'uploader_name', 'file_path', 'created_at')->get();
    }

    public function headings(): array
    {
        return [
            'Vendor Name',
            'Invoice Receipt Number',
            'Uploader Name',
            'File Path',
            'Created At',
        ];
    }
}
