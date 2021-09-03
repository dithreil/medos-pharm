<template>
    <div>
        <q-breadcrumbs class="q-mb-xl client-details__breadcrumbs">
            <q-breadcrumbs-el>
                <a class="q-breadcrumbs__el q-link" href="/admin">Панель Администратора</a>
            </q-breadcrumbs-el>
            <q-breadcrumbs-el
                :label="$loc('clientNameMultiple')"
                :to="{name: 'ClientList'}"
            />
            <q-breadcrumbs-el
                class="employee-details__breadcrumbs client-details__breadcrumbs_elStyle"
                icon="face"
            />
        </q-breadcrumbs>
        <div class="client">
            <q-card>
                <q-form
                    class="client__form col w-100"
                    ref="form"
                    @submit="save"
                >
                    <q-card-section>
                        <div class="text-h6">
                            {{$loc('clientCreate') }}
                        </div>
                        <p class="text-grey-7">
                            Поля, отмеченные знаком <span class="text-negative">*</span>, обязательны для заполнения
                        </p>
                    </q-card-section>
                    <q-card-section v-if="model" class="q-p-none">
                        <div class="client__input">
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
                        </div>
                        <div class="client__input">
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
                        </div>
                        <div class="client__input">
                            <q-input
                                v-model="model.patronymic"
                                outlined
                                label="Отчество"
                                lazy-rules
                                :rules="[
                                    (val) => ruLettersRule(val) || $errorMessages.ONLY_RU_LETTERS,
                                ]"
                            />
                        </div>
                        <div class="client__input">
                            <q-input
                                v-model="model.birthDate"
                                outlined
                                mask="##-##-####"
                                fill-mask
                                label="Дата рождения *"
                                lazy-rules
                                :rules="[
                                    (val) => dateFormat(val) || $errorMessages.INVALID_DATE,
                                    (val) => !!val || $errorMessages.REQUIRED,
                                    (val) => dateValidate(val, true) || 'Введите корректную дату рождения',
                                ]"
                                @blur="checkDate"
                            >
                                <template #append>
                                    <q-icon name="event" class="cursor-pointer">
                                        <q-popup-proxy ref="qDateProxy" transition-show="scale" transition-hide="scale">
                                            <q-date v-model="model.birthDate" mask="DD-MM-YYYY" minimal>
                                                <div class="row items-center justify-end">
                                                    <q-btn v-close-popup label="Закрыть" color="primary" flat />
                                                </div>
                                            </q-date>
                                        </q-popup-proxy>
                                    </q-icon>
                                </template>
                            </q-input>
                        </div>
                        <div class="client__input">
                            <q-input
                                v-model="model.phoneNumber"
                                outlined
                                mask="+7 (###) ###-##-##"
                                unmasked-value
                                fill-mask
                                label="Телефон *"
                                lazy-rules
                                :rules="[
                                    (val) => phoneRule(val) || $errorMessages.INVALID_PHONE,
                                    (val) => !!val || $errorMessages.REQUIRED,
                                ]"
                            />
                        </div>
                        <div class="client__input">
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
                        </div>
                        <div class="client__input">
                            <q-input
                                v-model="model.password"
                                outlined
                                label="Пароль *"
                                :rules="[
                                    (val) => !!val || $errorMessages.REQUIRED,
                                ]"
                            />
                        </div>
                        <div class="client__input client__input_mb">
                            <q-input
                                v-model="model.skype"
                                outlined
                                class="input-margin"
                                label="Skype"
                            />
                        </div>
                        <div class="client__input client__input_mb">
                            <WhatsAppInput v-model="model.whatsapp" />
                        </div>
                    </q-card-section>
                    <q-card-actions align="right">
                        <q-btn
                            v-close-popup
                            unelevated
                            label="Закрыть"
                            color="grey-5"
                            :to="{name: 'ClientList'}"
                        />
                        <q-btn
                            unelevated
                            label="Сохранить"
                            color="primary"
                            type="submit"
                        />
                    </q-card-actions>
                </q-form>
            </q-card>
        </div>
    </div>
</template>

<script>
import {mapActions} from 'vuex';
import {success} from '../../utils/notifizer';
import * as validationHelpers from '../../validation/helpers';
import CreateClientModel from '../../models/CreateClientModel';
import WhatsAppInput from '../../../../common/UI/CustomInput/WhatsAppInput.vue';
import {dateValidate} from '../../../../common/utils/dateValidate';
import {requiredFieldsMixin} from '../../../../common/mixins/requiredFieldsMixin';

export default {
    name: 'ClientCreate',
    components: {WhatsAppInput},
    mixins: [requiredFieldsMixin],
    data() {
        return {
            model: null,
        };
    },
    mounted() {
        this.model = JSON.parse(JSON.stringify(CreateClientModel));
    },
    methods: {
        dateValidate,
        ...mapActions({
            createUser: 'client/createUser',
        }),
        ...validationHelpers,
        checkDate() {
            const isValidBirthDate = this.$moment(this.model.birthDate, 'DD-MM-YYYY', true).isValid();
            if (!isValidBirthDate) {
                this.model.birthDate = '';
            }
        },
        isFormInvalid() {
            return this.$refs.form.validate()
                .then((success) => {
                    return success;
                });
        },
        async save() {
            const isValid = await this.isFormInvalid();

            if (!isValid) return;

            this.createUser({payload: this.model})
                .then((response) => {
                    if (response.status === 201) {
                        this.isActive = false;
                        success('Пользователь создан');
                        this.$router.push({name: 'ClientList'});
                    }
                });
        },
    },
};
</script>

<style lang="scss" scoped>
    .client {
        &__input {
            &_mb {
                margin-bottom: 20px;
            }
        }
        &__form {
            padding-bottom: 30px;
        }
    }
</style>
