@extends('layouts.admin') {{-- atau layouts.app untuk user --}}

@section('title', 'Upload Invoice')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm rounded p-4">
        <h2 class="mb-4">Upload Invoice</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Periksa kembali isian berikut:</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.invoices.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
    <label class="form-label fw-semibold">Nama Vendor</label>
    <input type="text" 
           name="vendor_name" 
           class="form-control rounded-pill" 
           value="{{ Auth::user()->name }}" 
           readonly>
</div>


            <div class="mb-3">
                <label class="form-label fw-semibold">Nomor Tanda Terima Invoice</label>
                <input type="text" name="invoice_receipt_number" class="form-control rounded-pill" placeholder="Masukkan Nomor Tanda Terima" value="{{ old('invoice_receipt_number') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Pengupload</label>
                <input type="text" name="uploader_name" class="form-control rounded-pill" placeholder="Masukkan Nama Pengupload" value="{{ old('uploader_name') }}" required>
            </div>

            <div class="mb-3">
    <label class="form-label fw-semibold">File Invoice (PDF/JPG/PNG/ZIP)</label>
    <input type="file" name="file" class="form-control rounded-pill" required>
</div>


            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill">Upload Invoice</button>
                <a href="{{ route('admin.invoices') }}" class="btn btn-secondary rounded-pill">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection
