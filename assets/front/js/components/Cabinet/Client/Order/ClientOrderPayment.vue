<template>
    <q-dialog ref="dialog" @hide="onDialogHide">
        <q-card class="q-dialog-plugin">
            <q-card-section>
                <p>Вы будете перенаправлены на страницу оплаты через</p>
                <div class="countdown">{{countdown}}</div>
            </q-card-section>
            <q-card-actions align="center">
                <q-btn color="positive" label="Перейти" @click="onOKClick" />
                <q-btn color="negative" label="Отмена" @click="onCancelClick" />
            </q-card-actions>
        </q-card>
    </q-dialog>
</template>

<script>
export default {
    name: 'ClientOrderPayment',
    data() {
        return {
            countdown: 3,
            timer: null,
        };
    },
    mounted() {
        this.startTimer();
    },
    destroyed() {
        this.stopTimer();
    },
    watch: {
        countdown(time) {
            if (time === 0) {
                this.onOKClick();
            }
        },
    },
    methods: {
        startTimer() {
            this.timer = setInterval(() => {
                this.countdown--;
            }, 1000);
        },
        stopTimer() {
            clearTimeout(this.timer);
        },
        show() {
            this.$refs.dialog.show();
        },
        hide() {
            this.$refs.dialog.hide();
        },
        onDialogHide() {
            this.$emit('hide');
        },
        onOKClick() {
            this.$emit('ok');
            this.hide();
        },
        onCancelClick() {
            this.hide();
        },
    },
};
</script>

<style scoped>
.countdown {
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 30px;
    font-weight: 500;
}
</style>
