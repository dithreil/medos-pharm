export default {
    BASE: '',
    AUTH: {
        LOGIN: '/api/security/login',
    },
    CLIENT: {
        DEFAULT: '/admin/api/clients',
        EDIT: (id, action) => `/admin/api/clients/${id}?action=${action}`,
        DETAILS: (id) => `/admin/api/clients/${id}`,
    },
    EMPLOYEE: {
        DEFAULT: '/admin/api/employees',
        EDIT: (id, action) => `/admin/api/employees/${id}?action=${action}`,
        DETAILS: (id) => `/admin/api/employees/${id}`,
        WEEKLY_SCHEDULES: '/admin/api/employees/weekly-schedules',
        DAY: '/admin/api/daily-schedules',
    },
    ORDER: {
        DEFAULT: '/admin/api/orders',
        EDIT: (id) => `/admin/api/orders/${id}`,
        DETAILS: (id) => `/admin/api/orders/${id}`,
        ADD_DOC: (id) => `/admin/api/orders/${id}/documents`,
        DELETE_DOC: (id) => `/admin/api/orders/order-documents/${id}`,
        GET_DOC: (id) => `/download/order-documents/${id}`,
    },
    PAYMENT: {
        DEFAULT: '/admin/api/payments/',
        DETAILS: (id) => `/admin/api/payments/${id}`,
    },
    AREA: {
        DEFAULT: '/admin/api/areas',
        DETAILS: (id) => `/admin/api/areas/${id}`,
    },
    SPECIALITIES: {
        DEFAULT: '/admin/api/specialities',
        DETAILS: (id) => `/admin/api/specialities/${id}`,
    },
    CATEGORIES: {
        DEFAULT: '/admin/api/categories',
    },
};
