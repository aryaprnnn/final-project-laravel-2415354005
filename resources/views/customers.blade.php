@extends('layouts.app')

@section('content')
    <div x-data="customersPage()">
        <div class="page-header">
            <div>
                <h2 class="page-title">Customers</h2>
                <p class="page-subtitle">Manage client directory and activation status.</p>
            </div>
            <div class="flex items-center space-x-3">
                <select onchange="window.location.href = '?deleted=' + this.value" class="form-select !w-40 !py-1.5 !text-[10px] font-bold uppercase tracking-wider">
                    <option value="hide" {{ request('deleted') == 'hide' ? 'selected' : '' }}>Aktif Saja</option>
                    <option value="show" {{ request('deleted') == 'show' ? 'selected' : '' }}>Semua Data</option>
                    <option value="only" {{ request('deleted') == 'only' ? 'selected' : '' }}>Terarsip (Sampah)</option>
                </select>
                <button @click="openAdd()" class="btn-primary">
                    <i data-lucide="plus" class="mr-2 w-4 h-4"></i> Add Customer
                </button>
            </div>
        </div>

        <div class="card-table">
            <div class="overflow-x-auto">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Customer ID</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border)]">
                        @forelse($customers as $customer)
                            <tr>
                                <td class="text-sm font-bold tracking-tight text-[var(--primary)]">{{ $customer->customer_id }}</td>
                                <td class="text-sm font-semibold text-[var(--text-main)]">{{ $customer->name }}</td>
                                <td class="text-xs">
                                    <div class="max-w-[200px] truncate text-[var(--text-main)]" title="{{ $customer->address }}">
                                        {{ $customer->address ?? '-' }}
                                    </div>
                                </td>
                                <td class="text-xs">
                                    <div class="text-[var(--text-main)]">{{ $customer->email ?? '-' }}</div>
                                    <div class="text-[var(--text-muted)] mt-0.5">{{ $customer->phone ?? '-' }}</div>
                                </td>
                                <td>
                                    @if($customer->trashed())
                                        <span class="badge-danger !bg-slate-500/10 !text-slate-500 !border-slate-500/20">ARCHIVED</span>
                                    @else
                                        <span class="{{ $customer->status ? 'badge-success' : 'badge-danger' }}">
                                            {{ $customer->status ? 'ACTIVE' : 'INACTIVE' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        @if(!$customer->trashed())
                                            <button @click="openEdit({{ json_encode($customer) }})" class="btn-icon-primary p-2">
                                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                                            </button>
                                            <button @click="deleteData({{ $customer->id }})" class="btn-icon-danger p-2 text-rose-500">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        @else
                                            <button @click="restoreData({{ $customer->id }})" class="btn-icon-primary p-2 text-emerald-500" title="Pulihkan">
                                                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-10 text-[var(--text-muted)]">No customers found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($customers->hasPages())
                <div class="p-4 border-t border-[var(--border)]">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>

        <div x-show="showModal" 
             class="fixed inset-0 z-[150] flex items-center justify-center p-4 md:p-6" 
             style="display: none;">
            
            <!-- Backdrop -->
            <div x-show="showModal" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="showModal = false" 
                 class="modal-backdrop"></div>

            <!-- Modal Content -->
            <div x-show="showModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-8"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-8"
                 class="modal-content w-full max-w-lg relative z-[160] max-h-[90vh] flex flex-col">
                
                <div class="modal-header shrink-0">
                    <div>
                        <h3 class="text-lg font-bold text-[var(--text-main)]" x-text="isEdit ? 'Edit Customer' : 'Add New Customer'"></h3>
                        <p class="text-[10px] text-[var(--text-muted)] font-medium">Please fill in the client details below.</p>
                    </div>
                    <button @click="showModal = false" class="p-2 text-[var(--text-muted)] hover:text-rose-500 transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="flex flex-col overflow-hidden">
                    <div class="p-8 space-y-6 overflow-y-auto">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="form-label">Customer ID</label>
                                <input type="text" x-model="formData.customer_id" required class="form-input" placeholder="e.g. CUST-001">
                            </div>
                            <div>
                                <label class="form-label">Full Name</label>
                                <input type="text" x-model="formData.name" required class="form-input" placeholder="e.g. Budi Santoso">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="form-label">Email Address</label>
                                <input type="email" x-model="formData.email" class="form-input" placeholder="customer@mail.com">
                            </div>
                            <div>
                                <label class="form-label">Phone Number</label>
                                <input type="text" x-model="formData.phone" class="form-input" placeholder="08xxxxxxxxxx">
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Home Address</label>
                            <textarea x-model="formData.address" rows="3" class="form-input resize-none" placeholder="Complete address..."></textarea>
                        </div>
                        <div class="flex items-center space-x-3 pt-2">
                            <label class="form-switch">
                                <input type="checkbox" x-model="formData.status" class="sr-only peer">
                                <div class="form-switch-track">
                                    <div class="form-switch-thumb"></div>
                                </div>
                                <span class="ml-3 text-sm font-bold text-[var(--text-main)]">Active Status</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" @click="showModal = false" class="btn-secondary-sm">Cancel</button>
                        <button type="submit" :disabled="loading" class="btn-primary-sm">
                            <span x-show="!loading" x-text="isEdit ? 'Update Data' : 'Save Customer'"></span>
                            <span x-show="loading">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
