@extends('layouts.app')

@section('content')
    <div x-data="invoicesPage()">
        <div class="page-header">
            <div>
                <h2 class="page-title">Invoices</h2>
                <p class="page-subtitle">Billing history and payment tracking.</p>
            </div>
        </div>

        <div class="card-table">
            <div class="overflow-x-auto">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Customer</th>
                            <th class="text-right">Amount</th>
                            <th>Status</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border)]">
                        @forelse($invoices as $inv)
                            <tr>
                                <td class="text-sm font-bold tracking-tight text-[var(--primary)]">{{ $inv->invoice_number }}</td>
                                <td class="text-sm font-semibold text-[var(--text-main)]">{{ $inv->subscription->customer->name }}</td>
                                <td class="text-right font-bold text-sm tracking-tighter text-[var(--text-main)]">Rp {{ number_format($inv->amount, 0, ',', '.') }}</td>
                                <td>
                                    <span class="{{ $inv->payment_status === 'paid' ? 'badge-success' : ($inv->payment_status === 'unpaid' ? 'badge-warning' : 'badge-danger') }}">
                                        {{ strtoupper($inv->payment_status) }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button @click="openEdit({{ json_encode($inv) }})" class="btn-icon-primary">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </button>
                                        <button @click="deleteData({{ $inv->id }})" class="btn-icon-danger">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-10 text-[var(--text-muted)]">No invoices found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Form (Quick Status Update) -->
        <div x-show="showModal" 
             class="fixed inset-0 z-[150] flex items-center justify-center p-4 overflow-y-auto" 
             style="display: none;">
            
            <div x-show="showModal" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="showModal = false" 
                 class="modal-backdrop"></div>

            <div x-show="showModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-8"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-8"
                 class="modal-content w-full max-w-sm relative z-[160] flex flex-col">
                
                <div class="modal-header shrink-0">
                    <div>
                        <h3 class="text-lg font-bold text-[var(--text-main)]">Update Payment</h3>
                        <p class="text-[10px] text-[var(--text-muted)] font-medium">Manually update the billing status.</p>
                    </div>
                    <button @click="showModal = false" class="p-2 text-[var(--text-muted)] hover:text-rose-500 transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="flex flex-col overflow-hidden">
                    <div class="p-8 space-y-6">
                        <div>
                            <label class="form-label">Select Payment Status</label>
                            <select x-model="formData.payment_status" required class="form-select">
                                <option value="unpaid">UNPAID</option>
                                <option value="paid">PAID</option>
                                <option value="expired">EXPIRED</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" @click="showModal = false" class="btn-secondary-sm">Cancel</button>
                        <button type="submit" :disabled="loading" class="btn-primary-sm">
                            <span x-show="!loading">Update Status</span>
                            <span x-show="loading">Updating...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
