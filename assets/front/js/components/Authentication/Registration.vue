<template>
    <q-page style="padding-bottom: 60px">
        <div class="text-center">
            <h1 class="text-h2 text-primary">
                Регистрация
            </h1>
            <p class="text-grey-7">
                Поля, отмеченные знаком <span class="text-negative">*</span>, обязательны для заполнения
            </p>
        </div>

        <div class="row justify-center items-center">
            <q-form
                ref="form"
                class="q-gutter-md col"
                style="max-width: 500px"
                @submit="send"
            >
                <q-input
                    v-model="model.lastName"
                    outlined
                    label="Фамилия *"
                    lazy-rules
                    :rules="[
                        (val) => ruLettersRule(val) || $errorMessages.ONLY_RU_LETTERS,
                        (val) => !!val || $errorMessages.REQUIRED,
                    ]"
                />
                <q-input
                    v-model="model.firstName"
                    outlined
                    label="Имя *"
                    lazy-rules
                    :rules="[
                        (val) => ruLettersRule(val) || $errorMessages.ONLY_RU_LETTERS,
                        (val) => !!val || $errorMessages.REQUIRED,
                    ]"
                />
                <q-input
                    v-model="model.patronymic"
                    outlined
                    label="Отчество"
                    lazy-rules
                    :rules="[
                        (val) => ruLettersRule(val) || $errorMessages.ONLY_RU_LETTERS,
                    ]"
                />
                <q-input
                    v-model="model.birthDate"
                    outlined
                    mask="##-##-####"
                    fill-mask
                    label="Дата рождения *"
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
                    label="Телефон *"
                    lazy-rules
                    unmasked-value
                    :rules="[
                        (val) => phoneRule(val) || $errorMessages.INVALID_PHONE,
                        (val) => !!val || $errorMessages.REQUIRED,
                    ]"
                />
                <q-input
                    v-model="model.email"
                    outlined
                    label="Email *"
                    lazy-rules
                    :rules="[
                        (val) => emailRule(val) || $errorMessages.INVALID_EMAIL,
                        (val) => !!val || $errorMessages.REQUIRED,
                    ]"
                />
                <q-input
                    v-model="model.password"
                    outlined
                    type="password"
                    label="Пароль *"
                    lazy-rules
                    :rules="[
                        (val) => val.length >= 4 || $errorMessages.INVALID_PASSWOR_LENGTH(4),
                        (val) => !!val || $errorMessages.REQUIRED,
                    ]"
                />
                <q-input
                    v-model="model.confirmPassword"
                    outlined
                    type="password"
                    label="Повторите пароль *"
                    lazy-rules
                    :rules="[
                        (val) => val === model.password || $errorMessages.INVALID_CONFIRM_PASSWORDS,
                        (val) => !!val || $errorMessages.REQUIRED,
                    ]"
                />
                <q-checkbox v-model="model.agreement" style="align-items: end;">
                    <template #default>
                        <p>Согласен на обработку персональных данных согласно
                            <a
                                :href="externalLink"
                                target="_blank"
                                rel="noopener noreferrer"
                                @click="toExternalSite"
                            >
                                ФЗ №152 «О персональных данных».
                            </a>
                        </p>
                    </template>
                </q-checkbox>
                <div class="column justify-start items-start mb-4">
                    <router-link
                        tag="a"
                        :to="{name: 'Authorization', params: {userType: 'client'}}"
                        class="q-mb-sm text-primary"
                    >
                        Авторизация
                    </router-link>
                    <router-link
                        tag="a"
                        :to="{name: 'PasswordRestore', params: {userType: 'client'}}"
                        class="q-mb-sm text-primary"
                    >
                        Забыли пароль?
                    </router-link>
                </div>

                <q-btn
                    color="primary"
                    size="md"
                    label="Зарегистироваться"
                    type="submit"
                    :disable="!model.agreement"
                />
            </q-form>
        </div>
    </q-page>
</template>

<script>
import * as validationHelpers from '../../validation/helpers';
import mask from '../../utils/mask-derective';
import {mapActions} from 'vuex';
import {dateValidate} from '../../../../common/utils';
import {currentLocale} from '../../../../common/constants';
import {requiredFieldsMixin} from '../../../../common/mixins/requiredFieldsMixin';

export default {
    name: 'Registration',
    directives: {
        mask,
    },
    mixins: [requiredFieldsMixin],
    data() {
        return {
            model: {
                lastName: '',
                firstName: '',
                patronymic: '',
                phoneNumber: '',
                birthDate: '',
                email: '',
                password: '',
                confirmPassword: '',
                agreement: false,
            },
            externalLink: 'http://www.kremlin.ru/acts/bank/24154',
        };
    },
    computed: {
        getCurrentLocale() {
            return currentLocale;
        },
    },
    methods: {
        ...validationHelpers,
        dateValidate,
        ...mapActions({
            registration: 'client/registration',
        }),
        isFormInvalid() {
            return this.$refs.form.validate();
        },
        toExternalSite() {
            window.open(this.externalLink);
        },
        async send() {
            const isValid = await this.isFormInvalid();

            if (!isValid) {
                return;
            }

            const payload = {...this.model};
            payload.phoneNumber = payload.phoneNumber.replace(/[^0-9]+/gi, '');
            this.registration(payload);
        },
    },
};
</script>
