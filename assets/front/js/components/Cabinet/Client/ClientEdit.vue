<template>
    <q-card
        flat
        bordered
        class="my-card bg-grey-1"
        style=" margin-bottom: 50px"
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
                v-model="model.lastName"
                outlined
                label="Фамилия"
                lazy-rules
                :rules="[
                    (val) => ruLettersRule(val) || $errorMessages.ONLY_RU_LETTERS,
                    (val) => !!val || $errorMessages.REQUIRED,
                    (val) => val.length < 85 || 'Максимум 85 символов!',
                ]"
            />
            <q-input
                v-model="model.firstName"
                outlined
                label="Имя"
                lazy-rules
                :rules="[
                    (val) => ruLettersRule(val) || $errorMessages.ONLY_RU_LETTERS,
                    (val) => !!val || $errorMessages.REQUIRED,
                    (val) => val.length < 85 || 'Максимум 85 символов!',
                ]"
            />
            <q-input
                v-model="model.patronymic"
                outlined
                label="Отчество"
                lazy-rules
                :rules="[
                    (val) => ruLettersRule(val) || $errorMessages.ONLY_RU_LETTERS,
                    (val) => val.length < 85 || 'Максимум 85 символов!',
                ]"
            />
            <q-input
                v-model="model.birthDate"
                outlined
                mask="##-##-####"
                fill-mask
                label="Дата рождения"
                lazy-rules
                :rules="[
                    (val) => !!val || $errorMessages.REQUIRED,
                    (val) => dateValidate(val, true) || 'Введите корректную дату рождения',
                ]"
            >
                <template #append>
                    <q-icon name="event" class="cursor-pointer">
                        <q-popup-proxy ref="qDateProxy" transition-show="scale" transition-hide="scale">
                            <q-date
                                v-model="model.birthDate"
                                :locale="getCurrentLocale"
                                mask="DD-MM-YYYY"
                                minimal
                            >
                                <div class="row items-center justify-end">
                                    <q-btn v-close-popup label="Закрыть" color="primary" flat />
                                </div>
                            </q-date>
                        </q-popup-proxy>
                    </q-icon>
                </template>
            </q-input>
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
            <q-input
                v-model="model.snils"
                outlined
                mask="###-###-### ##"
                unmasked-value
                fill-mask
                label="СНИЛС"
                class="input-margin"
            />
            <q-input
                v-model="model.skype"
                outlined
                class="input-margin"
                label="Skype"
            />
            <WhatsAppInput v-model="model.whatsapp" />
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
import {dateValidate} from '../../../../../common/utils';
import {currentLocale} from '../../../../../common/constants';
import WhatsAppInput from '../../../../../common/UI/CustomInput/WhatsAppInput.vue';

export default {
    name: 'ClientEdit',
    components: {WhatsAppInput},
    data() {
        return {
            model: null,
        };
    },
    computed: {
        ...mapGetters({
            userData: 'user/userData',
        }),
        getCurrentLocale() {
            return currentLocale;
        },
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
        dateValidate,
        ...validationHelpers,
        ...mapActions({
            saveClientData: 'client/saveClientData',
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

            const payload = {...this.model};
            payload.phoneNumber = payload.phoneNumber.replace(/[^0-9]+/gi, '');

            this.saveClientData(payload);
        },
    },
};
</script>
<style>
.input-margin {
    margin-bottom: 20px;
}
</style>
