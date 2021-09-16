export default {
    BASE: '',
    AUTH: {
        LOGIN: '/api/security/login',
    },
    PRODUCER: {
        DEFAULT: '/admin/api/producers',
        DETAILS: (id: string) => `/admin/api/producers/${id}`,
    },
    SUPPLIER: {
        DEFAULT: '/admin/api/suppliers',
        DETAILS: (id: string) => `/admin/api/suppliers/${id}`,
    },
    STORE: {
        DEFAULT: '/admin/api/stores',
        DETAILS: (id: string) => `/admin/api/stores/${id}`,
    },
    NOMENCLATURE: {
        DEFAULT: '/admin/api/nomenclatures',
        MED_FORMS: '/admin/api/nomenclatures/med-forms',
        DETAILS: (id: string) => `/admin/api/nomenclatures/${id}`,
    },
    INCOME: {
        DEFAULT: '/admin/api/incomes',
        DETAILS: (id: string) => `/admin/api/incomes/${id}`,
    },
};
