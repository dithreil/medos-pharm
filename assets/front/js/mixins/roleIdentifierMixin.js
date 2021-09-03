
export const roleIdentifierMixin = {
    computed: {
        isEmployee() {
            return !!this.$route.path.includes('employee');
        },
    },
};
