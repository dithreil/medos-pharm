export default {
    BASE: '',
    AUTH: {
        LOGIN: '/api/security/login',
    },
    PRODUCER: {
        DEFAULT: '/admin/api/producers',
        EDIT: (id) => `/admin/api/producers/${id}`,
        DETAILS: (id) => `/admin/api/producers/${id}`,
    },
};
