<template>
    <q-page >
        <h2 >Для совершения заказов авторизуйтесь</h2>
        <router-link class="auth-route" :to="{name: 'Authorization',params:{userType: 'client'}}">
            Войти как клиент
        </router-link>
        <router-link class="auth-route" :to="{name: 'Authorization',params:{userType: 'employee'}}">
            Войти как работник
        </router-link>
        <router-link class="auth-route" :to="{name: 'Registration'}">
            Регистрация
        </router-link>
    </q-page>
</template>

<script>
export default {
    name: 'NotAuthorized',

};
</script>
<style>
.auth-route{
    font-size: 20pt;
    display: block;
}
</style>
