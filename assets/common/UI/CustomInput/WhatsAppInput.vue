<template>
    <q-input
        v-model="inputValue"
        outlined
        :label="isRequired ? 'Логин (для WhatsApp)' : 'WhatsApp'"
        mask="+7 (###) ###-##-##"
        unmasked-value
        fill-mask
        lazy-rules
        :rules="[
            (val) => isRequired
                ? (!!val || $errorMessages.REQUIRED)
                : val.length < 11 || 'Не более 11 символов!',
        ]"
    />
</template>

<script>
export default {
    name: 'WhatsAppInput',
    props: {
        value: {
            type: String,
            required: true,
        },
        isRequired: {
            type: Boolean,
            default: false,
        },
    },
    computed: {
        inputValue: {
            get() {
                return this.value;
            },
            set(val) {
                this.$emit('input', val);
            },
        },
    },
};
</script>
