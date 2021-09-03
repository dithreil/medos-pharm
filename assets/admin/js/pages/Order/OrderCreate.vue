<template>
    <div style="padding-bottom: 60px;">
        <q-breadcrumbs class="q-mb-xl client-details__breadcrumbs">
            <q-breadcrumbs-el>
                <a class="q-breadcrumbs__el q-link" href="/admin">Панель Администратора</a>
            </q-breadcrumbs-el>
            <q-breadcrumbs-el
                label="Консультации"
                :to="{name: 'OrderList'}"
            />
            <q-breadcrumbs-el
                class="employee-details__breadcrumbs client-details__breadcrumbs_elStyle"
                icon="face"
            />
        </q-breadcrumbs>
        <q-card  class="order">
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
                            <q-select
                                v-model="model.client"
                                emit-value
                                map-options
                                :options="clientsData ? clientsData.items : []"
                                :label="$loc('clientFullName')"
                                use-input
                                option-label="fullName"
                                outlined
                                input-debounce="300"
                                lazy-rules
                                :rules="[
                                    (val) => !!val || $errorMessages.REQUIRED,
                                ]"
                                behavior="menu"
                                @filter="fetchClients"
                                @input="setClientData"
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
                    v-if="weekScheduleData"
                    :type="visitType()"
                    :data="weekSchedule"
                    :clientId="model.client.id"
                />
            </template>
        </q-card>
    </div>
</template>

<script>
import {mapActions, mapGetters, mapMutations} from 'vuex';
import WeekScheduleTable from '../../../../common/UI/EmployeeWeeklySchedule/WeekScheduleTable.vue';
import {orderCreateMixin} from '../../../../common/mixins/orderCreateMixin';
import WhatsAppInput from '../../../../common/UI/CustomInput/WhatsAppInput.vue';
import Loader from '../../../../common/UI/Loader/Loader.vue';
import {isDateInPast} from '../../../../common/utils/isDateInPast';

export default {
    name: 'OrderCreate',
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
            clientsData: 'client/usersData',
        }),
        weekScheduleData() {
            return !!this.weekSchedule?.length ?? false;
        },
    },
    methods: {
        ...mapActions({
            updateWeekScheduleRequestParams: 'employee/updateWeekScheduleRequestParams',
            getCategoriesList: 'category/getCategoryList',
            updateUserRequestParams: 'client/updateUserRequestParams',
            getSpecialityDetails: 'speciality/getSpecialityDetails',
            getAreaDetails: 'area/getAreaDetails',
        }),
        ...mapMutations({
            updateCurrentCategory: 'category/updateCurrentCategory',
        }),
        setClientData({skype, whatsapp}) {
            this.model.skype = skype;
            this.model.whatsapp = whatsapp;
        },
        fetchClients(val, update, abort) {
            if (val.length < 3) {
                abort();

                return;
            }
            this.updateUserRequestParams({pagination: this.pagination, filter: val})
                .finally(() => {
                    update();
                });
        },
        pastDateValidation() {
            isDateInPast(this.model.date) ? this.model.date : this.model.date = '';
        },
        async fetchWeekSchedule() {
            const isValid = await this.isFormInvalid();
            if (!isValid) return;

            this.loading = true;

            try {
                const {data: categoriesData} = await this.getCategoriesList();
                this.model.category.id = categoriesData.items
                    .find((item) => item.name === this.model.category.label)?.id;

                const {data: specialityData} = await this.getSpecialityDetails(this.model.speciality.id);
                this.model.speciality = specialityData;
                const {data: areaData} = await this.getAreaDetails(this.model.area.id);
                this.model.area = areaData;

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
