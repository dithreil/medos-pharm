<template>
    <q-layout>
        <q-page-container class="cabinet-container">
            <q-page>
                <div class="row justify-between items-center">
                    <h1 class="text-h2 text-primary text-primary_modidfy">
                        Личный кабинет
                    </h1>
                </div>
                <router-view/>
            </q-page>
        </q-page-container>
        <NavMenu />
        <UserNotifier />
    </q-layout>
</template>

<script>
import UserNotifier from '../Cabinet/Common/Misc/UserNotifier.vue';
import NavMenu from '../Cabinet/Common/Misc/NavMenu';

export default {
    name: 'Cabinet',
    components: {NavMenu, UserNotifier},
};
</script>
