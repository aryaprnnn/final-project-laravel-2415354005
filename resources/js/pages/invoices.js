import { getCsrfToken, notify, reloadPage } from '../utils';

export default () => ({
    showModal: false, 
    formData: { id: '', payment_status: 'unpaid' },
    loading: false,
    
    async submitForm() {
        this.loading = true;
        try {
            const res = await fetch(`/api/invoices/${this.formData.id}`, {
                method: 'PUT',
                headers: { 
                    'Content-Type': 'application/json', 
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify({ payment_status: this.formData.payment_status })
            });
            const data = await res.json();
            
            if (res.ok) {
                notify(data.message, 'success');
                reloadPage();
            } else {
                notify(data.message || 'Gagal memperbarui invoice', 'error');
            }
        } catch (e) { 
            notify('Something went wrong', 'error');
        }
        finally { this.loading = false; }
    },

    async deleteData(id) {
        window.confirmAction({
            title: 'Hapus Invoice?',
            message: 'Apakah Anda yakin ingin menghapus invoice ini?',
            confirmLabel: 'Ya, Hapus',
            onConfirm: async () => {
                try {
                    const res = await fetch(`/api/invoices/${id}`, { 
                        method: 'DELETE', 
                        headers: { 
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken()
                        } 
                    });
                    const data = await res.json();
                    if(res.ok) {
                        notify(data.message, 'success');
                        reloadPage();
                    } else {
                        notify(data.message, 'error');
                    }
                } catch (e) { 
                    notify('Gagal menghapus invoice', 'error');
                }
            }
        });
    },

    openEdit(inv) {
        this.formData = { id: inv.id, payment_status: inv.payment_status };
        this.showModal = true;
    }
});
