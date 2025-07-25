@extends('layouts.admin')

@section('title', 'Daftar Invoice')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
    .sort-icon {
        margin-left: 5px;
        font-size: 0.8em;
    }
</style>

<div class="container mt-5">

    {{-- Header & Tombol Aksi --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h2 class="mb-3 mb-md-0">Daftar Invoice</h2>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.invoices.create') }}" class="btn btn-primary rounded-pill">
                <i class="bi bi-plus-circle"></i> Upload Invoice
            </a>
            <a href="{{ route('admin.invoices.export') }}" class="btn btn-success rounded-pill">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
        </div>
    </div>

    {{-- Notifikasi Sukses --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Pencarian --}}
    <div class="mb-4">
        <input type="text" id="tableSearch" class="form-control rounded-pill" placeholder="Cari invoice...">
    </div>

    {{-- Tabel Invoice --}}
    <div class="table-responsive shadow-sm rounded">
        <table class="table table-bordered table-hover align-middle text-center mb-0" id="invoiceTable" data-sortdir="asc">
            <thead class="table-light">
                <tr>
                    <th onclick="sortTable(0, this)" style="cursor:pointer;"># <i class="bi bi-arrow-down-up sort-icon"></i></th>
                    <th onclick="sortTable(1, this)" style="cursor:pointer;">Vendor <i class="bi bi-arrow-down-up sort-icon"></i></th>
                    <th onclick="sortTable(2, this)" style="cursor:pointer;">Nomor <i class="bi bi-arrow-down-up sort-icon"></i></th>
                    <th onclick="sortTable(3, this)" style="cursor:pointer;">Pengupload <i class="bi bi-arrow-down-up sort-icon"></i></th>
                    <th>File</th>
                    <th onclick="sortTable(5, this)" style="cursor:pointer;">Tanggal <i class="bi bi-arrow-down-up sort-icon"></i></th>
                    <th>Status</th>
                    <th>Tanggal Update Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="invoiceTableBody">
                @foreach ($invoices as $index => $invoice)
                <tr class="{{ $invoice->trashed() ? 'table-secondary' : '' }}">

                    <td>{{ $index + 1 }}</td>
                    <td>{{ $invoice->vendor_name }}</td>
                    <td>{{ $invoice->invoice_receipt_number }}</td>
                    <td>{{ $invoice->uploader_name }}</td>
                    <td>
                        <a href="{{ route('admin.invoices.download', $invoice->id) }}" class="btn btn-sm btn-primary rounded-pill">
                            <i class="bi bi-download"></i> Download
                        </a>
                    </td>
                    <td>{{ $invoice->created_at->format('d-m-Y') }}</td>
                    <td>{{ $invoice->status }}</td>
                    <td>{{ $invoice->status_updated_at ? $invoice->status_updated_at->format('d-m-Y H:i') : '-' }}</td>
                    <td>
                        @if (!$invoice->trashed())
                            <form action="{{ route('admin.invoices.done', $invoice->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm rounded-pill">Done</button>
                            </form>

                            <form action="{{ route('admin.invoices.destroy', $invoice->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus invoice ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm rounded-pill">Delete</button>
                            </form>
                        @else
                            <button class="btn btn-secondary btn-sm rounded-pill" disabled>Done</button>
                            <button class="btn btn-secondary btn-sm rounded-pill" disabled>Delete</button>
                        @endif
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function refreshInvoiceTable() {
    fetch("{{ route('admin.invoices.fetch') }}")
        .then(response => response.json())
        .then(result => {
            const tbody = document.getElementById('invoiceTableBody');
            tbody.innerHTML = '';

            result.data.forEach((invoice, index) => {
                tbody.innerHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${invoice.vendor_name}</td>
                        <td>${invoice.invoice_receipt_number}</td>
                        <td>${invoice.uploader_name}</td>
                        <td>
                            <a href="{{ route('admin.invoices.download', ':id') }}" class="btn btn-sm btn-primary rounded-pill" data-id="${invoice.id}">
                                <i class="bi bi-download"></i> Download
                            </a>
                        </td>
                        <td>${new Date(invoice.created_at).toLocaleDateString('id-ID')}</td>
                    </tr>
                `;
            });

            const downloadLinks = document.querySelectorAll('#invoiceTableBody .btn.btn-sm.btn-primary.rounded-pill');
            downloadLinks.forEach(link => {
                link.onclick = () => {
                    window.location.href = link.getAttribute('href').replace(':id', link.getAttribute('data-id'));
                }
            });
        });
}

// Auto refresh setiap 10 detik
setInterval(refreshInvoiceTable, 10000);
</script>


<script>
function sortTable(columnIndex, header) {
    const table = document.getElementById("invoiceTable");
    const rows = Array.from(table.rows).slice(1);
    const asc = table.getAttribute('data-sortdir') !== 'asc';

    rows.sort((a, b) => {
        const valA = a.cells[columnIndex].innerText.trim().toLowerCase();
        const valB = b.cells[columnIndex].innerText.trim().toLowerCase();

        if (!isNaN(Date.parse(valA)) && !isNaN(Date.parse(valB))) {
            return asc ? new Date(valA) - new Date(valB) : new Date(valB) - new Date(valA);
        }

        return asc ? valA.localeCompare(valB) : valB.localeCompare(valA);
    });

    rows.forEach(row => table.tBodies[0].appendChild(row));
    table.setAttribute('data-sortdir', asc ? 'asc' : 'desc');

    document.querySelectorAll('.sort-icon').forEach(icon => {
        icon.className = 'bi bi-arrow-down-up sort-icon';
    });

    const icon = header.querySelector('.sort-icon');
    icon.className = asc ? 'bi bi-arrow-up sort-icon' : 'bi bi-arrow-down sort-icon';
}

document.getElementById('tableSearch').addEventListener('keyup', function() {
    const searchValue = this.value.toLowerCase();
    const rows = document.querySelectorAll('#invoiceTable tbody tr');
    rows.forEach(row => {
        const rowText = row.innerText.toLowerCase();
        row.style.display = rowText.includes(searchValue) ? '' : 'none';
    });
});
</script>
@endsection

