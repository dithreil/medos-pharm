<template>
    <div style="float: right; ">
        <q-card class="nav-menu" bordered flat>
            <q-btn
                class="full-width"
                flat
                :color="$route.name === btn.link ? 'primary' : 'standart'"
                :to="{name: btn.link}"
                v-for="btn in buttons"
                :key="btn.link"
            >
                {{btn.label}}
            </q-btn>
            <q-btn
                class="full-width"
                flat
                @click="logout"
            >
                Выход
            </q-btn>
        </q-card>
    </div>
</template>

<script>
import {mapActions} from 'vuex';
import {roleIdentifierMixin} from '../../../../mixins/roleIdentifierMixin';

export default {
    name: 'NavMenu',
    mixins: [roleIdentifierMixin],
    computed: {
        buttons() {
            return this.isEmployee
                ? [
                    {link: 'EmployeeInfo', label: 'Мой профиль'},
                    {link: 'EmployeeOrdersList', label: 'Мои консультации'}]
                : [
                    {link: 'Appointment', label: 'Записаться на прием'},
                    {link: 'ClientOrdersList', label: 'Мои консультации'},
                    {link: 'ClientInfo', label: 'Мой профиль'}];
        },
    },
    methods: {
        ...mapActions({
            logout: 'authentication/logout',
        }),
    },
};
</script>
