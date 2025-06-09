export const api = {
    get: async (url, action, params = {}) => {
        let full_url = `${url}?action=${action}&${new URLSearchParams(params).toString()}`;
        const response = await fetch(full_url);
        return response.json();
    },

    post: async (url, action, data) => {
        const formData = new FormData();
        formData.append('action', action);
        for (const key in data) {
            formData.append(key, data[key]);
        }
        
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });
        return response.json();
    }
};