export default {
    isGranted(role) {
        const roles = window.sfUserRoles || [];

        return -1 !== roles.indexOf(role);
    },
};
