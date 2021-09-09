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
};
