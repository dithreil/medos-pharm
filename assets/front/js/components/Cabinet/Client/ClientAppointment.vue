<template>
    <q-card
        flat
        bordered
        class="my-card bg-grey-3"
    >
        <div class="card-header">
            <h4 class="card-header card-header_title">
                Записаться на прием
            </h4>
            <q-separator />
        </div>
        <q-form
            ref="form"
            class="employee__form"
            @submit="save"
        >
            <q-card-section>
                <div>
                    <p class="q-textsubtitle1">Выберите способ консультации:</p>
                    <q-option-group
                        v-model="radioLoginType"
                        :options="radioOptions"
                        color="primary"
                        inline
                    />
                </div>
            </q-card-section>
            <q-card-section class="q-p-none row">
                <div class="col-7">
                    <div>
                        <q-input
                            v-model="model.skype"
                            outlined
                            v-if="radioLoginType === 'skype'"
                            label="Логин (для Skype)"
                            lazy-rules
                            :rules="[
                                (val) => !!val || $errorMessages.REQUIRED,
                            ]"
                        />
                    </div>
                    <div v-if="radioLoginType === 'whatsapp'">
                        <WhatsAppInput v-model="model.whatsapp" :isRequired="true" />
                    </div>
                    <div>
                        <q-select
                            v-model="model.category"
                            emit-value
                            map-options
                            :options="categories || []"
                            label="Категория"
                            use-input
                            option-label="label"
                            outlined
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
                    </div>
                    <div>
                        <q-select
                            v-model="model.area"
                            emit-value
                            map-options
                            :options="filterAreas || [] "
                            label="Регион"
                            use-input
                            option-label="name"
                            outlined
                            @filter="fetchAreas"
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
                    </div>
                    <div>
                        <q-select
                            v-model="model.speciality"
                            emit-value
                            map-options
                            :options="filterSpecialities || []"
                            :label="model.area ? 'Специальность' : 'Сначала выберите регион'"
                            use-input
                            option-label="name"
                            outlined
                            :disable="!model.area"
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
                    </div>
                </div>
                <div class="col-4 offset-1">
                    <q-date
                        v-model="model.date"
                        mask="YYYY-MM-DD"
                        :locale="getCurrentLocale"
                        :rules="[
                            (val) => !!val || $errorMessages.REQUIRED,
                        ]"
                        minimal
                        @input="pastDateValidation"
                    />
                </div>
            </q-card-section>
            <q-card-actions align="center">
                <q-btn
                    unelevated
                    label="Посмотреть расписание"
                    color="primary"
                    @click="fetchWeekSchedule"
                />
            </q-card-actions>
        </q-form>
        <template v-if="loading">
            <div class="flex items-center justify-center q-pt-md">
                <Loader />
            </div>
        </template>
        <template v-else>
            <WeekScheduleTable
                v-if="weekSchedule"
                :type="visitType()"
                :data="weekSchedule"
            />
        </template>
    </q-card>
</template>

<script>
import {mapActions, mapGetters, mapMutations} from 'vuex';
import WeekScheduleTable from '../../../../../common/UI/EmployeeWeeklySchedule/WeekScheduleTable.vue';
import {isDateInPast} from '../../../../../common/utils/isDateInPast';
import {orderCreateMixin} from '../../../../../common/mixins/orderCreateMixin';
import {currentLocale} from '../../../../../common/constants';
import WhatsAppInput from '../../../../../common/UI/CustomInput/WhatsAppInput.vue';
import Loader from '../../../../../common/UI/Loader/Loader.vue';

export default {
    name: 'ClientAppointment',
    components: {WeekScheduleTable, WhatsAppInput, Loader},
    mixins: [orderCreateMixin],
    data() {
        return {
            loading: false,
        };
    },
    computed: {
        ...mapGetters({
            weekSchedule: 'employee/weekSchedule',
            areaData: 'area/areasData',
            userData: 'user/userData',
        }),
        getCurrentLocale() {
            return currentLocale;
        },
    },
    mounted() {
        if (this.userData) {
            this.model.skype = this.userData.skype;
            this.model.whatsapp = this.userData.whatsapp;
        }
    },
    methods: {
        ...mapActions({
            updateWeekScheduleRequestParams: 'employee/updateWeekScheduleRequestParams',
            getCategoriesList: 'category/getCategoriesList',
        }),
        ...mapMutations({
            updateCurrentCategory: 'category/updateCurrentCategory',
        }),
        pastDateValidation() {
            isDateInPast(this.model.date) ? this.model.date : this.model.date = '';
        },
        async fetchWeekSchedule() {
            const isValid = await this.isFormInvalid();
            if (!isValid) return;

            this.loading = true;

            try {
                const {data} = await this.getCategoriesList();
                this.model.category.id = data.items.find((item) => item.name === this.model.category.label)?.id;

                const dataToServer = {
                    areaCode: this.model.area.code,
                    specialityCode: this.model.speciality.code,
                    categoryCode: this.model.category.code,
                    date: this.model.date,
                };
                await this.updateWeekScheduleRequestParams(dataToServer);
                this.updateCurrentCategory(this.model.category);
                this.loading = false;
            } catch (error) {
                this.loading = false;
            }
        },
        async save() {
            this.updateWeekScheduleRequestParams({model: this.model});
        },
    },
};
</script>

