<template>
    <div class="flex justify-between items-center topbar bg-white blue-grey-10">
        <div class="flex items-center no-wrap">
            <div class="sidebar-burger">
                <span
                    @click="toogleSiedeBar"
                    v-if="isOpen"
                    class="material-icons text-primary opened">menu_open
                </span>
                <span
                    @click="toogleSiedeBar"
                    v-else
                    class="material-icons text-primary opened">menu
                </span>
            </div>

            <div class="topbar__title topbar__title_flex">
                <a
                    class="q-ma-none text-primary text-subtitle1"
                    href="/admin"
                >
                    Панель Администратора</a>
            </div>
        </div>
        <div class="flex items-center">
            <div class="flex">
                <span class="material-icons text-primary q-mr-xs">exit_to_app</span>
                <a class="text-primary" href="/logout">Выйти</a>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Topbar',
    data() {
        return {
            isOpen: true,
        };
    },
    methods: {
        toogleSiedeBar() {
            this.isOpen = !this.isOpen;
            this.$emit('moveSiedeBar', this.isOpen);
        },
    },
};
</script>
