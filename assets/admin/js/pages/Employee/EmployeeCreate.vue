<template>
    <div>
        <q-breadcrumbs class="q-mb-xl employee-details__breadcrumbs">
            <q-breadcrumbs-el icon="home">
                <a class="q-breadcrumbs__el q-link" href="/admin">Панель Администратора</a>
            </q-breadcrumbs-el>
            <q-breadcrumbs-el
                icon="widgets"
                label="Сотрудники"
                :to="{name: 'EmployeeList'}"
            />
            <q-breadcrumbs-el
                icon="face"
                class="employee-details__breadcrumbs employee-details__breadcrumbs_elStyle"
            />
        </q-breadcrumbs>
        <div class="employee">
            <q-card >
                <q-form
                    ref="form"
                    class="employee__form"
                    @submit="save"
                >
                    <q-card-section>
                        <div class="text-h6">
                            {{$loc('employeeCreate')}}
                        </div>
                    </q-card-section>
                    <q-card-section class="q-p-none">
                        <div class="employee__input">
                            <q-input
                                v-model="model.lastName"
                                outlined
                                label="Фамилия"
                                lazy-rules
                                :rules="[
                                    (val) => ruLettersRule(val) || $errorMessages.ONLY_RU_LETTERS,
                                    (val) => !!val || $errorMessages.REQUIRED,
                                ]"
                            />
                        </div>
                        <div class="employee__input">
                            <q-input
                                v-model="model.firstName"
                                outlined
                                label="Имя"
                                lazy-rules
                                :rules="[
                                    (val) => ruLettersRule(val) || $errorMessages.ONLY_RU_LETTERS,
                                    (val) => !!val || $errorMessages.REQUIRED,
                                ]"
                            />
                        </div>
                        <div class="employee__input">
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
                        <q-select
                            v-model="model.speciality"
                            emit-value
                            map-options
                            :options="specialitiesData ? specialitiesData.items : []"
                            :label="$loc('specialityNameSingle')"
                            use-input
                            option-label="name"
                            outlined
                            input-debounce="300"
                            @filter="fetchSpecialities"
                            lazy-rules
                            :rules="[
                                (val) => !!val || $errorMessages.REQUIRED,
                            ]"
                            behavior="menu"
                        >
                            <template v-slot:no-option>
                                <q-item>
                                    <q-item-section class="text-grey">
                                        Нет результата
                                    </q-item-section>
                                </q-item>
                            </template>
                        </q-select>
                        <div class="employee__input">
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
                        </div>
                        <div class="employee__input">
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
                        </div>
                        <div class="employee__input">
                            <q-input
                                v-model="model.password"
                                outlined
                                label="Пароль"
                            />
                        </div>
                    </q-card-section>
                    <q-card-actions align="right">
                        <q-btn
                            v-close-popup
                            unelevated
                            label="Закрыть"
                            color="grey-5"
                            :to="{name: 'EmployeeList'}"
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
import {mapActions, mapGetters} from 'vuex';
import {success} from '../../utils/notifizer';
import * as validationHelpers from '../../validation/helpers';
import CreateEmployeeModel from '../../models/CreateEmployeeModel';

export default {
    name: 'EmployeeCreate',
    data() {
        return {
            isActive: false,
            model: '',
            userId: '',
            pagination: {
                sortBy: 'name',
                descending: false,
                page: 1,
                rowsPerPage: 10,
                rowsNumber: 0,
            },
        };
    },
    computed: {
        ...mapGetters({
            specialitiesData: 'speciality/specialitiesData',
        }),
    },
    methods: {
        ...mapActions({
            createUser: 'employee/createUser',
            updateSpecialitiesRequestParams: 'speciality/updateSpecialityRequestParams',
        }),
        fetchSpecialities(val, update, abort) {
            if (val.length < 3) {
                abort();

                return;
            }
            this.updateSpecialitiesRequestParams({pagination: this.pagination, filter: val})
                .finally(() => {
                    update();
                });
        },
        ...validationHelpers,
        toggleModalActivity() {
            this.model = JSON.parse(JSON.stringify(CreateEmployeeModel));
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

            this.createUser({payload: this.model})
                .then((response) => {
                    if (response.status === 201) {
                        this.isActive = false;
                        success('Пользователь создан');
                    }
                });
        },
    },
};
</script>
