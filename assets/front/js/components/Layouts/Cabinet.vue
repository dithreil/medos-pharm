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
    </q-layout>
</template>

<script>


export default {
    name: 'Cabinet',
};
</script>
