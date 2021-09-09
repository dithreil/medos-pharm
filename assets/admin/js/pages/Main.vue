<template>
    <div class="container">
        <div class="container__block ">5 не оплаченных заказов</div>
        <div class="container__block">17 заказов</div>
        <div class="container__block">189 оплаченных заказов</div>
    </div>
</template>

<script lang="ts">

import {Component, Vue} from 'vue-property-decorator';

@Component
export default class Main extends Vue {}
</script>
