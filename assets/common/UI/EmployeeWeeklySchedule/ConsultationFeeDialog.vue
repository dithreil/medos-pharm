<template>
    <q-dialog ref="dialog" @hide="onDialogHide">
        <q-card class="q-dialog-plugin">
            <q-card-section>
                <div class="text-h6">Вы записались на консультацию.</div>
                <p>Дата: {{date}}.</p>
                <p>Врач: {{employee}}</p>
                <p>Вам необходимо оплатить её.</p>
                <p>Оплатить здесь?</p>
            </q-card-section>
            <q-card-actions align="center">
                <q-btn color="primary" label="Да" @click="onOKClick" />
                <q-btn color="primary" label="Нет" @click="onCancelClick" />
            </q-card-actions>
        </q-card>
    </q-dialog>
</template>

<script>
export default {
    name: 'ConsultationFeeDialog',
    props: {
        date: {
            type: String,
            required: true,
        },
        employee: {
            type: String,
            required: true,
        },
    },
    methods: {
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
