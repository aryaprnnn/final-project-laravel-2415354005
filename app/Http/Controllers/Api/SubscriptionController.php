<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Menampilkan semua daftar langganan (bisa difilter)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Subscription::with(['customer', 'service']);

        // Filter berdasarkan Customer ID jika ada (?customer_id=1)
        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->query('customer_id'));
        }

        // Filter berdasarkan Service ID jika ada (?service_id=2)
        if ($request->has('service_id')) {
            $query->where('service_id', $request->query('service_id'));
        }

        // Filter berdasarkan Status jika ada (?status=active)
        if ($request->has('status')) {
            $query->where('status', $request->query('status'));
        }

        // Ambil data terbaru
        $subscriptions = $query->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Subscriptions retrieved successfully',
            'data' => $subscriptions,
        ]);
    }

    /**
     * Membuat data langganan baru (API Create)
     */
    public function store(Request $request): JsonResponse
    {
        // Proses Validasi Aturan Transaksi
        $validated = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['nullable', 'string', 'in:active,inactive,trial,isolir,dismantle'],
        ]);

        // Menyimpan Transaksi Langganan ke Database
        $subscription = Subscription::create($validated);

        // Eager Loading otomatis agar respon JSON langsung menampilkan data detail Customer & Service
        $subscription->load(['customer', 'service']);

        // Mengembalikan Respon Sukses (HTTP Status 201 Created)
        return response()->json([
            'success' => true,
            'message' => 'Subscription created successfully',
            'data' => $subscription,
        ], 201);
    }

    /**
     * Menampilkan detail data langganan berdasarkan ID (API Read Detail)
     */
    public function show(string $id): JsonResponse
    {
        // Mencari data langganan sekaligus mengangkut data relasi customer dan service
        $subscription = Subscription::with(['customer', 'service'])->find($id);

        // Kondisi jika ID transaksi tidak ditemukan (HTTP Status 404)
        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Subscription detail retrieved successfully',
            'data' => $subscription,
        ]);
    }

    /**
     * Memperbarui data langganan berdasarkan ID (API Update)
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $subscription = Subscription::find($id);

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription not found',
            ], 404);
        }

        // Proses validasi data baru (menggunakan rule 'sometimes')
        $validated = $request->validate([
            'customer_id' => ['sometimes', 'required', 'integer', 'exists:customers,id'],
            'service_id' => ['sometimes', 'required', 'integer', 'exists:services,id'],
            'start_date' => ['sometimes', 'required', 'date'],
            'end_date' => ['sometimes', 'required', 'date', 'after_or_equal:start_date'],
            'status' => ['sometimes', 'required', 'string', 'in:active,inactive,trial,isolir,dismantle'],
        ]);

        // Eksekusi pembaruan data ke database
        $subscription->update($validated);

        // Muat ulang data relasi terbaru agar respon JSON sinkron
        $subscription->load(['customer', 'service']);

        return response()->json([
            'success' => true,
            'message' => 'Subscription updated successfully',
            'data' => $subscription,
        ]);
    }

    /**
     * Menghapus data langganan berdasarkan ID (API Delete)
     */
    public function destroy(string $id): JsonResponse
    {
        $subscription = Subscription::find($id);

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription not found',
            ], 404);
        }

        // Eksekusi penghapusan data dari database
        $subscription->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subscription deleted successfully',
        ]);
    }
}