<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Menampilkan semua daftar invoice / tagihan (bisa difilter)
     */
    public function index(Request $request): JsonResponse
    {
        // Eager Loading bertingkat untuk mengambil data relasi sampai ke ujung
        $query = Invoice::with(['subscription.customer', 'subscription.service']);

        // Filter berdasarkan status pembayaran jika ada (?payment_status=unpaid)
        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->query('payment_status'));
        }

        $invoices = $query->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Invoices retrieved successfully',
            'data' => $invoices,
        ]);
    }

    /**
     * Membuat data invoice baru secara otomatis (API Create)
     */
    public function store(Request $request): JsonResponse
    {
        // Validasi untuk memastikan ID Subscription yang dikirim itu valid
        $validated = $request->validate([
            'subscription_id' => ['required', 'integer', 'exists:subscriptions,id'],
        ]);

        // Ambil data transaksi langganan beserta data harganya dari tabel service
        $subscription = \App\Models\Subscription::with('service')->find($validated['subscription_id']);

        // Otomatisasi pembuatan Nomor Invoice Unik (Contoh hasil: INV-20260522-0001)
        $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad((string)$subscription->id, 4, '0', STR_PAD_LEFT);

        // Menyimpan data invoice ke database
        $invoice = Invoice::create([
            'subscription_id' => $subscription->id,
            'invoice_number'  => $invoiceNumber,
            'amount'          => $subscription->service->price, // Mengambil nominal otomatis dari harga layanan
            'payment_status'  => 'unpaid',
        ]);

        // Muat data relasi penuh untuk respon JSON ke Postman
        $invoice->load(['subscription.customer', 'subscription.service']);

        return response()->json([
            'success' => true,
            'message' => 'Invoice generated successfully',
            'data' => $invoice,
        ], 201);
    }

    /**
     * Menampilkan detail data invoice berdasarkan ID (API Read Detail)
     */
    public function show(string $id): JsonResponse
    {
        $invoice = Invoice::with(['subscription.customer', 'subscription.service'])->find($id);

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Invoice detail retrieved successfully',
            'data' => $invoice,
        ]);
    }

    /**
     * Memperbarui status pembayaran invoice (API Update / Simulasi Payment)
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found',
            ], 404);
        }

        // Validasi status pembayaran
        $validated = $request->validate([
            'payment_status' => ['required', 'string', 'in:unpaid,paid,expired'],
        ]);

        // Logika otomatis: jika status diubah jadi 'paid', isi kolom 'paid_at' dengan waktu sekarang
        if ($validated['payment_status'] === 'paid') {
            $validated['paid_at'] = now(); // Mengambil timestamp real-time saat ini
        } else {
            $validated['paid_at'] = null;
        }

        $invoice->update($validated);
        $invoice->load(['subscription.customer', 'subscription.service']);

        return response()->json([
            'success' => true,
            'message' => 'Invoice payment status updated successfully',
            'data' => $invoice,
        ]);
    }

    /**
     * Menghapus data invoice dari database (API Delete)
     */
    public function destroy(string $id): JsonResponse
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found',
            ], 404);
        }

        $invoice->delete();

        return response()->json([
            'success' => true,
            'message' => 'Invoice deleted successfully',
        ]);
    }
}