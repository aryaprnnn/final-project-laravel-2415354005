<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Menampilkan daftar semua layanan (bisa difilter berdasarkan status)
     */
    public function index(Request $request): JsonResponse
    {
        // Mengambil parameter query '?status=' dari URL API
        $status = $request->query('status');

        $query = Service::query();

        // Validasi: Jika user mengirim filter status, pastikan nilainya cuma 'active' atau 'inactive'
        if ($status !== null) {
            if (!in_array($status, ['active', 'inactive'], true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => [
                        'status' => ['The selected status is invalid.'],
                    ],
                ], 422);
            }

            // Jika ?status=active, cari yang status databasenya true (1). Jika inactive, cari yang false (0)
            $query->where('status', $status === 'active');
        }

        // Ambil data terbaru dari database
        $services = $query->latest()->get();

        // Kembalikan respon berupa JSON data
        return response()->json([
            'success' => true,
            'message' => 'Services retrieved successfully',
            'data' => $services,
        ]);
    }

    /**
     * Menyimpan layanan digital baru ke database (API Create)
     */
    public function store(Request $request): JsonResponse
    {
        // Proses Validasi Inputan User
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'boolean'],
        ]);

        // Menyimpan Data Baru Menggunakan Model Service (Memanfaatkan $fillable)
        $service = Service::create($validated);

        // Mengembalikan Respon Sukses Beserta Data yang Baru Terbuat (HTTP Status 201)
        return response()->json([
            'success' => true,
            'message' => 'Service created successfully',
            'data' => $service,
        ], 201);
    }

    /**
     * Menampilkan detail data layanan berdasarkan ID (API Read Detail)
     */
    public function show(string $id): JsonResponse
    {
        // Mencari data di tabel services berdasarkan ID yang dikirim
        $service = Service::find($id);

        // Kondisi jika data tidak ditemukan di database (HTTP Status 404)
        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found',
            ], 404);
        }

        // Mengembalikan respon sukses beserta data detail layanan (HTTP Status 200)
        return response()->json([
            'success' => true,
            'message' => 'Service detail retrieved successfully',
            'data' => $service,
        ]);
    }

    /**
     * Memperbarui data layanan berdasarkan ID (API Update)
     */
    public function update(Request $request, string $id): JsonResponse
    {
        // Cari data layanan yang ingin diubah
        $service = Service::find($id);

        // Kondisi jika data tidak ditemukan (HTTP Status 404)
        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found',
            ], 404);
        }

        // Proses Validasi Inputan Baru (Gunakan 'sometimes' agar kolom yang tidak dikirim tidak error)
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'price' => ['sometimes', 'required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'required', 'boolean'],
        ]);

        // Update data ke database
        $service->update($validated);

        // Mengembalikan respon sukses beserta data yang telah diperbarui (HTTP Status 200)
        return response()->json([
            'success' => true,
            'message' => 'Service updated successfully',
            'data' => $service,
        ]);
    }

    /**
     * Menghapus data layanan berdasarkan ID (API Delete)
     */
    public function destroy(string $id): JsonResponse
    {
        // Cari data layanan yang ingin dihapus
        $service = Service::find($id);

        // Kondisi jika data tidak ditemukan (HTTP Status 404)
        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found',
            ], 404);
        }

        // Aturan Bisnis: Cegah penghapusan jika layanan masih terikat ke data Subscription
        // Kita memanfaatkan fungsi relasi subscriptions() yang sudah dibuat di Model Service sebelumnya
        // if ($service->subscriptions()->exists()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Cannot delete service. This service is currently used by active subscriptions.',
        //     ], 400);
        // }

        // Hapus data dari database jika lolos pengecekan relasi
        $service->delete();

        // Mengembalikan respon sukses penghapusan (HTTP Status 200)
        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully',
        ]);
    }
} 
