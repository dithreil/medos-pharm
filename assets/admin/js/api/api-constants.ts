export default {
    BASE: '',
    AUTH: {
        LOGIN: '/api/security/login',
    },
    PRODUCER: {
        DEFAULT: '/admin/api/producers',
        EDIT: (id: string) => `/admin/api/producers/${id}`,
        DETAILS: (id: string) => `/admin/api/producers/${id}`,
    },
    SUPPLIER: {
        DEFAULT: '/admin/api/suppliers',
        EDIT: (id: string) => `/admin/api/suppliers/${id}`,
        DETAILS: (id: string) => `/admin/api/suppliers/${id}`,
    },
};
