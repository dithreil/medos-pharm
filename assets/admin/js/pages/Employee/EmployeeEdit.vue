<template>
    <div>
        <q-breadcrumbs class="q-mb-xl employee-details__breadcrumbs">
            <q-breadcrumbs-el icon="home">
                <a class="q-breadcrumbs__el q-link" href="/admin">Панель Администратора</a>
            </q-breadcrumbs-el>
            <q-breadcrumbs-el
                icon="widgets"
                :label="$loc('employeeNameMultiple')"
                :to="{name: 'EmployeeList'}"
            />
            <q-breadcrumbs-el
                icon="face"
                :to="{name: 'EmployeeDetails'}"
                :label="`Информация о ${model.fullName}`"
            />
            <q-breadcrumbs-el
                class="employee-details__breadcrumbs employee-details__breadcrumbs_elStyle"
                label="Редактирование"
            />
        </q-breadcrumbs>
        <q-card class="emploee-edit">
            <q-form
                class="emploee-edit__form col w-100"
                ref="form"
                @submit="save"
            >
                <q-card-section>
                    <div class="text-h6">
                        {{$loc('employeeEdit')}}
                    </div>
                </q-card-section>
                <q-card-section class="q-p-none">
                    <q-input
                        v-model="model.phoneNumber"
                        outlined
                        mask="+7 (###) ###-##-##"
                        fill-mask
                        unmasked-value
                        label="Телефон"
                        lazy-rules
                        :rules="[
                            (val) => phoneRule(val) || $errorMessages.INVALID_PHONE,
                            (val) => !!val || $errorMessages.REQUIRED,
                        ]"
                    />
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
                </q-card-section>
                <q-card-actions align="right">
                    <q-btn
                        v-close-popup
                        unelevated
                        label="Закрыть"
                        @click="$router.push({name: 'EmployeeDetails', params: {id: id}});"
                        color="grey-5"
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

export default {
    name: 'EmployeeEdit',
    props: {
        id: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            isActive: false,
            model: {
                lastName: '',
                firstName: '',
                patronymic: '',
                fullName: '',
                phoneNumber: '',
                email: '',
                speciality: '',
            },
        };
    },
    mounted() {
        this.getUserDetails(this.id)
            .then((response) => {
                this.model = response.data;
            });
    },
    methods: {
        ...mapActions({
            getUserDetails: 'employee/getUserDetails',
            editUserData: 'employee/editUserData',
        }),
        ...validationHelpers,
        isFormInvalid() {
            return this.$refs.form.validate()
                .then((success) => {
                    return success;
                });
        },
        async save() {
            const isValid = await this.isFormInvalid();

            if (!isValid) return;

            this.editUserData({id: this.id, payload: this.model})
                .then((response) => {
                    if (response.status === 200) {
                        this.isActive = false;
                        success('Данные успешно изменены');
                        this.$router.push({name: 'EmployeeDetails', params: {id: this.id}});
                    }
                });
        },
    },
};
</script>

