<template>
    <q-card
        flat
        bordered
        class="my-card bg-grey-1"
        style=" margin-bottom: 50px; max-width: 900px;"
    >
        <div class="card-header">
            <h3 class="text-h4">
                Редактирование профиля
            </h3>
        </div>
        <q-form
            ref="form"
            class="q-gutter-md col"
            style="max-width: 500px;"
            @submit="save"
        >
            <q-input
                v-model="model.email"
                outlined
                label="Email"
                lazy-rules
                :rules="[
                    (val) => emailRule(val) || $errorMessages.INVALID_EMAIL,
                    (val) => !!val || $errorMessages.REQUIRED,
                ]"
            />
            <q-input
                v-model="model.phoneNumber"
                outlined
                mask="+7 (###) ###-##-##"
                unmasked-value
                fill-mask
                label="Телефон"
                lazy-rules
                :rules="[
                    (val) => phoneRule(val) || $errorMessages.INVALID_PHONE,
                    (val) => !!val || $errorMessages.REQUIRED,
                ]"
            />
            <q-btn
                color="positive"
                unelevated
                label="Сохранить"
                type="submit"
            />

            <q-btn
                color="negative"
                unelevated
                label="Закрыть"
                @click="$router.go(-1)"
            />
        </q-form>
    </q-card>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import * as validationHelpers from '../../../validation/helpers';

export default {
    name: 'EmployeeEdit',
    data() {
        return {
            model: null,
        };
    },
    computed: {
        ...mapGetters({
            userData: 'user/userData',
        }),
    },
    watch: {
        userData: {
            immediate: true,
            handler(n) {
                this.model = n;
            },
        },
    },
    methods: {
        ...validationHelpers,
        ...mapActions({
            saveEmployeeData: 'employee/saveEmployeeData',
        }),
        isFormInvalid() {
            return this.$refs.form.validate()
                .then((success) => {
                    return (success);
                });
        },
        async save() {
            const isValid = await this.isFormInvalid();

            if (!isValid) {
                return;
            }

            const payload = {
                phoneNumber: this.model.phoneNumber,
                email: this.model.email,
            };
            payload.phoneNumber = payload.phoneNumber.replace(/[^0-9]+/gi, '');
            await this.saveEmployeeData(payload);
            this.$router.replace({name: 'EmployeeInfo'});
        },
    },
};
</script>
<style>
.input-margin{
    margin-bottom: 20px;
}
</style>
