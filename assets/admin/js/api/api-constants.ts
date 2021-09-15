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
    STORE: {
        DEFAULT: '/admin/api/stores',
        EDIT: (id: string) => `/admin/api/stores/${id}`,
        DETAILS: (id: string) => `/admin/api/stores/${id}`,
    },
    NOMENCLATURE: {
        DEFAULT: '/admin/api/nomenclatures',
        MED_FORMS: '/admin/api/nomenclatures/med-forms',
        EDIT: (id: string) => `/admin/api/nomenclatures/${id}`,
        DETAILS: (id: string) => `/admin/api/nomenclatures/${id}`,
    },
};
