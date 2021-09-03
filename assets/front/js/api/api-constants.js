export default {
    BASE: '',
    AUTH: {
        LOGIN: '/api/security/login',
        LOGOUT: '/api/security/logout',
        PASSWORD_RESTORE: {
            CLIENT: '/api/clients/restore-password',
            EMPLOYEE: '/api/employees/restore-password',
        },
        CHANGE_PASSWORD: '/api/security/change-password',
    },
    CLIENT: {
        DEFAULT: '/api/clients',
    },
    EMPLOYEE: {
        DEFAULT: '/api/employees',
        DAY: '/api/daily-schedules',
    },
    USER: {
        DEFAULT: '/api/users',
    },
    AREA: {
        DEFAULT: '/api/areas',
    },
    SPECIALITIES: {
        DEFAULT: '/api/specialities',
    },
    ORDER: {
        DEFAULT: '/api/orders',
        DETAILS: (id) => `/api/orders/${id}`,
        ADD_DOC: (id) => `/api/orders/${id}/documents`,
        DELETE_DOC: (id) => `/api/orders/order-documents/${id}`,
        GET_DOC: (id) => `/download/order-documents/${id}`,
    },
    CATEGORY: {
        DEFAULT: '/api/categories',
    },
    PAYMENT: {
        DEFAULT: '/api/payments',
    },
};
