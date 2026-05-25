import { getCsrfToken, notify, reloadPage } from '../utils';

export default () => ({
    showModal: false, 
    isEdit: false,
    formData: { id: '', name: '', price: 0, description: '', status: true },
    loading: false,
    
    async submitForm() {
        this.loading = true;
        const url = this.isEdit ? `/api/services/${this.formData.id}` : '/api/services';
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
            title: 'Hapus Layanan?',
            message: 'Apakah Anda yakin ingin menghapus layanan ini? Layanan yang memiliki langganan aktif tidak dapat dihapus.',
            confirmLabel: 'Ya, Hapus',
            onConfirm: async () => {
                try {
                    const res = await fetch(`/api/services/${id}`, { 
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

    openAdd() {
        this.isEdit = false;
        this.formData = { id: '', name: '', price: 0, description: '', status: true };
        this.showModal = true;
    },

    openEdit(serv) {
        this.isEdit = true;
        this.formData = { ...serv };
        this.showModal = true;
    }
});
