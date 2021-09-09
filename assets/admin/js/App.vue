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

<script lang="ts">

import Topbar from './components/Topbar.vue';
import Sidebar from './components/Sidebar.vue';
import {Component, Vue} from 'vue-property-decorator';

@Component({components: {Sidebar, Topbar}})
export default class App extends Vue {
    isActive = true;
    moveSiedeBar(isOpen: boolean) {
        this.isActive = isOpen;
    }
};
</script>
