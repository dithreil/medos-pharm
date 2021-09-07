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
    USER: {
        DEFAULT: '/api/users',
    },
};
