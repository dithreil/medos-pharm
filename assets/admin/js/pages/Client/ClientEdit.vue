<template>
    <div class="client">
        <q-breadcrumbs class="q-mb-xl employee-details__breadcrumbs">
            <q-breadcrumbs-el icon="home">
                <a class="q-breadcrumbs__el q-link" href="/admin">Панель Администратора</a>
            </q-breadcrumbs-el>
            <q-breadcrumbs-el
                icon="widgets"
                label="Клиенты"
                :to="{name: 'ClientList'}"
            />
            <q-breadcrumbs-el
                icon="face"
                :to="{name: 'ClientDetails'}"
                :label="`Информация о ${model.fullName}`"
            />
            <q-breadcrumbs-el
                class="employee-details__breadcrumbs employee-details__breadcrumbs_elStyle"
                label="Редактирование"
            />
        </q-breadcrumbs>
        <q-card
            style="max-width: 500px"
            class="client__edit">
            <q-form
                ref="form"
                class="col w-100"
                @submit="save"
            >
                <q-card-section>
                    <div class="text-h6">
                        {{ $loc('clientEdit')}}
                    </div>
                </q-card-section>
                <q-card-section class="q-p-none">
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
                                    <q-date v-model="model.birthDate" mask="DD-MM-YYYY" minimal>
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
                        label="Телефон *"
                        lazy-rules
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
                        v-model="model.snils"
                        outlined
                        mask="###-###-### ##"
                        unmasked-value
                        fill-mask
                        label="СНИЛС"
                        style="margin-bottom: 20px;"
                    />
                    <q-input
                        v-model="model.skype"
                        outlined
                        style="margin-bottom: 20px;"
                        label="Skype"
                    />
                    <WhatsAppInput v-model="model.whatsapp" />
                </q-card-section>
                <q-card-actions align="right">
                    <q-btn
                        v-close-popup
                        unelevated
                        label="Закрыть"
                        color="grey-5"
                        @click="$router.push({name: 'ClientDetails', params: {id: id}})"
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
</template>

<script>
import {mapActions} from 'vuex';
import {success} from '../../utils/notifizer';
import * as validationHelpers from '../../validation/helpers';
import {dateValidate} from '../../../../common/utils';
import WhatsAppInput from '../../../../common/UI/CustomInput/WhatsAppInput.vue';

export default {
    name: 'ClientEdit',
    components: {WhatsAppInput},
    props: {
        id: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            userId: '',
            model: {
                lastName: '',
                firstName: '',
                patronymic: '',
                phoneNumber: '',
                birthDate: '',
                email: '',
                snils: '',
                skype: '',
                whatsapp: '',
            },
        };
    },
    methods: {
        dateValidate,
        ...mapActions({
            editUserData: 'client/editUserData',
            getUserDetails: 'client/getUserDetails',
        }),
        ...validationHelpers,
        toggleModalActivity(userData = null) {
            if (!userData) {
                return;
            }

            this.userId = userData.id;
            for (const key in this.model) {
                if (key) {
                    this.model[key] = userData[key];
                }
            }

            this.isActive = !this.isActive;
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

            this.editUserData({id: this.userId, payload: this.model})
                .then((response) => {
                    if (response.status === 204) {
                        this.isActive = false;
                        success('Данные успешно изменены');
                    }
                });
        },
    },
    mounted() {
        this.getUserDetails(this.id)
            .then((response) => {
                this.model = response.data;
            });
    },
    watch: {
        id: {
            handler() {
                this.userId = this.id;
            },
            immediate: true,
        },
    },
};
</script>
