@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div>
            <h2 class="page-title">Dashboard Overview</h2>
            <p class="page-subtitle">Centralized monitoring of your business operations and billing activity.</p>
        </div>
    </div>

    <!-- Grid Statistik -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="stat-card">
            <div class="text-[var(--text-muted)] text-[9px] font-bold uppercase tracking-wider">Total Customers</div>
            <div class="text-2xl font-bold mt-0.5 tracking-tight text-[var(--text-main)]">{{ number_format($stats['total_customers']) }}</div>
        </div>
        <div class="stat-card">
            <div class="text-[var(--text-muted)] text-[9px] font-bold uppercase tracking-wider">Layanan Aktif</div>
            <div class="text-2xl font-bold mt-0.5 tracking-tight text-[var(--text-main)]">{{ number_format($stats['active_services']) }}</div>
        </div>
        <div class="stat-card">
            <div class="text-[var(--text-muted)] text-[9px] font-bold uppercase tracking-wider">Subscriptions</div>
            <div class="text-2xl font-bold mt-0.5 tracking-tight text-[var(--text-main)]">{{ number_format($stats['active_subscriptions']) }}</div>
        </div>
        <div class="stat-card">
            <div class="text-[var(--text-muted)] text-[9px] font-bold uppercase tracking-wider">Unpaid Invoices</div>
            <div class="text-2xl font-bold mt-0.5 tracking-tight text-[var(--text-main)]">{{ number_format($stats['unpaid_invoices']) }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6">
        <!-- Recent Invoices Table -->
        <div class="card-table">
            <div class="flex items-center justify-between p-4 border-b border-[var(--border)] bg-gray-50/50 dark:bg-gray-800/20">
                <h3 class="font-semibold text-base text-[var(--text-main)]">Tagihan Terbaru</h3>
                <a href="{{ route('invoices') }}" class="text-xs font-semibold text-[var(--primary)] hover:underline flex items-center tracking-wider">
                    LIHAT SEMUA <i data-lucide="chevron-right" class="ml-1 w-3 h-3"></i>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Nomor Invoice</th>
                            <th>Pelanggan</th>
                            <th>Layanan</th>
                            <th>Status</th>
                            <th class="text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border)]">
                        @forelse($recentInvoices as $inv)
                            <tr class="cursor-default">
                                <td class="font-bold tracking-tight text-sm text-[var(--text-main)]">{{ $inv->invoice_number }}</td>
                                <td class="text-sm font-semibold text-[var(--text-main)]">
                                    {{ $inv->subscription->customer->name ?? 'Customer Deleted' }}
                                    @if($inv->subscription->customer?->trashed())
                                        <span class="text-[9px] px-1.5 py-0.5 rounded-sm bg-slate-500/10 text-slate-500 ml-1">ARCHIVED</span>
                                    @endif
                                </td>
                                <td class="text-xs">
                                    <div class="font-medium text-[var(--primary)]">{{ $inv->subscription->service->name ?? '-' }}</div>
                                </td>
                                <td>
                                    <span class="{{ $inv->payment_status === 'paid' ? 'badge-success' : ($inv->payment_status === 'unpaid' ? 'badge-warning' : 'badge-danger') }}">
                                        {{ strtoupper($inv->payment_status) }}
                                    </span>
                                </td>
                                <td class="text-right font-bold text-sm tracking-tighter text-[var(--text-main)]">Rp {{ number_format($inv->amount, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-10 text-[var(--text-muted)]">Belum ada data tagihan terbaru.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection