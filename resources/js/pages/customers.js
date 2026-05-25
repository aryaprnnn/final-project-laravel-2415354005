import { getCsrfToken, notify, reloadPage } from '../utils';

export default () => ({
    showModal: false, 
    isEdit: false,
    formData: { id: '', customer_id: '', name: '', email: '', phone: '', address: '', status: true },
    loading: false,
    
    async submitForm() {
        this.loading = true;
        const url = this.isEdit ? `/api/customers/${this.formData.id}` : '/api/customers';
        const method = this.isEdit ? 'PUT' : 'POST';
        
        try {
            const res = await fetch(url, {
                method: method,
                headers: { 
                    'Content-Type': 'application/json', 
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify(this.formData)
            });
            const data = await res.json();
            
            if (res.ok) {
                notify(data.message, 'success');
                reloadPage();
            } else {
                const errorMsg = data.errors ? Object.values(data.errors)[0][0] : data.message;
                notify(errorMsg, 'error');
            }
        } catch (e) {
            notify('Something went wrong', 'error');
        } finally {
            this.loading = false;
        }
    },

    async deleteData(id) {
        window.confirmAction({
            title: 'Hapus Customer?',
            message: 'Data customer ini akan dihapus secara permanen dari sistem.',
            confirmLabel: 'Ya, Hapus',
            onConfirm: async () => {
                try {
                    const res = await fetch(`/api/customers/${id}`, { 
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
                    notify('Gagal menghapus data', 'error');
                }
            }
        });
    },

    async restoreData(id) {
        window.confirmAction({
            title: 'Pulihkan Customer?',
            message: 'Customer ini akan dikembalikan ke daftar aktif.',
            confirmLabel: 'Ya, Pulihkan',
            onConfirm: async () => {
                try {
                    const res = await fetch(`/api/customers/${id}/restore`, { 
                        method: 'POST', 
                        headers: { 
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken()
                        } 
                    });
                    const data = await res.json();
                    if(res.ok) {
                        notify(data.message, 'success');
                        // Beri sedikit jeda agar user bisa baca notifikasi sebelum refresh
                        reloadPage(1500);
                    } else {
                        notify(data.message || 'Gagal memulihkan data', 'error');
                    }
                } catch (e) { 
                    notify('Gagal memulihkan data (Network Error)', 'error'); 
                }
            }
        });
    },

    openAdd() {
        this.isEdit = false;
        this.formData = { id: '', customer_id: '', name: '', email: '', phone: '', address: '', status: true };
        this.showModal = true;
    },

    openEdit(cust) {
        this.isEdit = true;
        this.formData = { ...cust };
        this.showModal = true;
    }
});
