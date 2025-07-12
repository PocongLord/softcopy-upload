<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    public function index()
{
   $invoices = \App\Models\Invoice::withTrashed()
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

    return view('invoices.index', compact('invoices'));
}



    public function create()
    {
        return view('invoices.create');
    }

    public function store(Request $request)
    {
        $request->validate([
    'vendor_name' => 'required|string|max:255',
    'invoice_receipt_number' => 'required|string|max:100',
    'uploader_name' => 'required|string|max:255',
    'file' => 'required|file|mimes:pdf,jpg,jpeg,png,zip|max:5120', // max 5MB
]);



        $filePath = $request->file('file')->store('invoices', 'public');

        Invoice::create([
            'vendor_name' => $request->vendor_name,
            'invoice_receipt_number' => $request->invoice_receipt_number,
            'uploader_name' => $request->uploader_name,
            'file_path' => $filePath,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('invoices.index')->with('success', 'Invoice berhasil diupload.');
    }
    public function adminIndex(Request $request)
{
    $search = $request->input('search');

   $invoices = \App\Models\Invoice::withTrashed()
    ->when($search, function ($query, $search) {
        $query->where('vendor_name', 'like', "%{$search}%")
              ->orWhere('invoice_receipt_number', 'like', "%{$search}%")
              ->orWhere('uploader_name', 'like', "%{$search}%");
    })
    ->latest()
    ->get();

    return view('admin.invoices.index', compact('invoices', 'search'));
}


public function userDownload(Invoice $invoice)
{
    if ($invoice->user_id !== Auth::id()) {
        abort(403, 'Akses ditolak');
    }

    return Storage::disk('public')->download($invoice->file_path);
}


public function adminCreate()
{
    return view('admin.invoices.create');
}

public function adminStore(Request $request)
{
    $request->validate([
    'vendor_name' => 'required|string|max:255',
    'invoice_receipt_number' => 'required|string|max:100',
    'uploader_name' => 'required|string|max:255',
    'file' => 'required|file|mimes:pdf,jpg,jpeg,png,zip|max:5120', // max 5MB
]);



    $filePath = $request->file('file')->store('invoices', 'public');

    \App\Models\Invoice::create([
        'vendor_name' => $request->vendor_name,
        'invoice_receipt_number' => $request->invoice_receipt_number,
        'uploader_name' => $request->uploader_name,
        'file_path' => $filePath,
        'user_id' => auth()->id(),
    ]);

    return redirect()->route('admin.invoices')->with('success', 'Invoice berhasil diupload.');
}


public function markAsDone($id)
{
    $invoice = \App\Models\Invoice::findOrFail($id);
    $invoice->status = 'File sudah diverifikasi';
    $invoice->status_updated_at = now();
    $invoice->save();

    return redirect()->route('admin.invoices')->with('success', 'Invoice berhasil diverifikasi.');
}

public function destroy($id)
{
    $invoice = \App\Models\Invoice::findOrFail($id);
    $invoice->status = 'Dihapus oleh admin';
    $invoice->status_updated_at = now();
    $invoice->save();
    $invoice->delete();

    return redirect()->route('admin.invoices')->with('success', 'Invoice berhasil dihapus.');
}

public function download(\App\Models\Invoice $invoice)
{
    return Storage::disk('public')->download($invoice->file_path);
}


public function fetchInvoices()
{
    $invoices = Invoice::withTrashed()
        ->where('user_id', auth()->id())
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json([
        'data' => $invoices
    ]);
}

public function fetchInvoicesAdmin(Request $request)
{
    $search = $request->input('search');

    $invoices = Invoice::withTrashed()
        ->when($search, function ($query, $search) {
            $query->where('vendor_name', 'like', "%{$search}%")
                  ->orWhere('invoice_receipt_number', 'like', "%{$search}%")
                  ->orWhere('uploader_name', 'like', "%{$search}%");
        })
        ->orderByDesc('created_at')
        ->get();

    return response()->json(['data' => $invoices]);
}



}
