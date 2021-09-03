<template>
    <div>
        <div class="main-wrapper column no-wrap">
            <Topbar @moveSiedeBar="moveSiedeBar"/>
            <Sidebar :isActive="isActive"/>
            <div class="content-wrapper">
                <div class="page-content">
                    <q-layout>
                        <q-page-container>
                            <q-page>
                                <router-view />
                            </q-page>
                        </q-page-container>
                    </q-layout>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Topbar from './components/Topbar';
import Sidebar from './components/Sidebar';
export default {
    name: 'App',
    components: {Sidebar, Topbar},
    data() {
        return {
            isActive: true,
        };
    },
    methods: {
        moveSiedeBar(isOpen) {
            this.isActive = isOpen;
        },
    },
};
</script>
