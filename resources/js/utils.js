export const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

export const notify = (message, type = 'success') => {
    window.dispatchEvent(new CustomEvent('notify', { detail: { message, type } }));
};

export const reloadPage = (delay = 1000) => {
    setTimeout(() => window.location.reload(), delay);
};
