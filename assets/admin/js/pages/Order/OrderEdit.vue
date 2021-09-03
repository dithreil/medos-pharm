<template>
    <q-card v-if="model">
        <q-form
            ref="form"
            class="col"
            style="max-width: 700px;"
            @submit="save"
        >
            <q-card-section>
                <div class="text-h6">
                    {{$loc('orderEdit')}}
                </div>
            </q-card-section>
            <q-card-section v-if="model" class="q-p-none">
                <q-select
                    v-model="model.employee"
                    emit-value
                    map-options
                    :options="employeesData ? employeesData.items : []"
                    :label="$loc('employeeFullName')"
                    use-input
                    option-label="fullName"
                    outlined
                    input-debounce="300"
                    lazy-rules
                    :rules="[
                        (val) => !!val || $errorMessages.REQUIRED,
                    ]"
                    behavior="menu"
                    @filter="fetchEmployees"
                    @input="setEmployeeData"
                >
                    <template v-slot:no-option>
                        <q-item>
                            <q-item-section class="text-grey">
                                Нет результата
                            </q-item-section>
                        </q-item>
                    </template>
                </q-select>
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
                <div class="row">
                    <div class="col">
                        <q-date
                            v-model="model.date"
                            mask="YYYY-MM-DD"
                            :rules="[
                                (val) => !!val || $errorMessages.REQUIRED,
                            ]"
                            minimal
                            @input="getEmployeeDaySchedule"
                        />
                    </div>
                    <div class="col">
                        <template v-if="timeOptions.length">
                            <q-select
                                filled
                                v-model="selectedTime"
                                multiple
                                :options="timeOptions"
                                counter
                                max-values="4"
                                use-chips
                                stack-label
                                autofocus
                                hint="Максимально 4 временных отрезка подряд"
                                hide-hint
                                label="Выберите временной отрезок"
                                style="width: 250px"
                                @input="validateTimePeriod"
                            />
                        </template>
                        <template v-else-if="isDayDisabled !== null && !isDayDisabled">
                            <p>В этот день приёма нет или врач уже занят!</p>
                        </template>
                        <template v-else>
                            <p>Выберите желаемую дату!</p>
                        </template>
                    </div>
                </div>
                <q-option-group
                    v-if="model.communication"
                    v-model="model.communication"
                    :options="communicationOptions"
                    color="primary"
                    inline
                />
                <div v-if="model.communication === 'skype'">
                    <q-input
                        v-model="model.clientTarget"
                        outlined
                        label="Логин (для Skype)"
                        lazy-rules
                        :rules="[
                            (val) => !!val || $errorMessages.REQUIRED,
                        ]"
                    />
                </div>
                <div v-if="model.communication === 'whats app'" >
                    <WhatsAppInput v-model="model.clientTarget" :isRequired="true" />
                </div>
                <q-input
                    v-model="model.employeeComment"
                    label="Введите ваш комментарий"
                    autogrow
                    class="q-mb-md"
                />
                <q-select
                    v-model="model.status"
                    :options="statusOptions"
                    outlined
                    label="Статус консультации"
                />
            </q-card-section>
            <q-card-actions align="right">
                <q-btn
                    unelevated
                    label="Сохранить"
                    color="primary"
                    type="submit"
                />
            </q-card-actions>
        </q-form>
    </q-card>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import * as validationHelpers from '../../validation/helpers';
import {orderStatuses, categories} from '../../../../common/constants';
import WhatsAppInput from '../../../../common/UI/CustomInput/WhatsAppInput.vue';
import {error} from '../../utils/notifizer';
import {isDateInPast} from '../../../../common/utils/isDateInPast';

export default {
    name: 'OrderEdit',
    components: {WhatsAppInput},
    props: {
        id: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            model: null,
            communicationOptions: [
                {label: 'Skype', value: 'skype'},
                {label: 'WhatsApp', value: 'whats app'},
            ],
            daySchedule: [],
            employee: null,
            timeOptions: [],
            selectedTime: [],
            isDayDisabled: null,
        };
    },
    computed: {
        ...mapGetters({
            employeesData: 'employee/usersData',
        }),
        statusOptions() {
            return orderStatuses.map((item) => ({
                label: item.label,
                value: item.key,
            }));
        },
        categories() {
            return categories;
        },
    },
    async mounted() {
        try {
            const {data} = await this.getOrderDetails(this.id);
            this.model = data;
            this.model.status = this.statusOptions.find((item) => item.value === this.model.status).label;
            const {data: employeeData} = await this.getUserDetails(this.model.employee.id);
            this.employee = employeeData;
        } catch (error) {
            console.log(error);
        }
    },
    methods: {
        ...mapActions({
            editOrderData: 'order/editOrderData',
            updateUserRequestParams: 'client/updateUserRequestParams',
            updateEmployeeRequestParams: 'employee/updateUserRequestParams',
            getOrderDetails: 'order/getOrderDetails',
            getCategoriesList: 'category/getCategoryList',
            getUserDetails: 'employee/getUserDetails',
            updateDayScheduleRequestParams: 'employee/updateDayScheduleRequestParams',
        }),
        ...validationHelpers,
        fetchEmployees(val, update, abort) {
            if (val.length < 3) {
                abort();

                return;
            }
            this.updateEmployeeRequestParams({pagination: this.pagination, filter: val})
                .finally(() => {
                    update();
                });
        },
        setEmployeeData(value) {
            this.employee = value;
        },
        async getEmployeeDaySchedule() {
            if (!this.pastDateValidation()) return;

            const {data: categoriesData} = await this.getCategoriesList();
            const categoryCode = categoriesData.items
                .find((item) => item.name === this.model.category)?.code;

            const dataToServer = {
                areaCode: this.employee.areaCode,
                specialityCode: this.employee.specialityCode,
                employeeCode: this.employee.code,
                categoryCode,
                date: this.model.date,
            };

            const {data} = await this.updateDayScheduleRequestParams(dataToServer);
            this.daySchedule = data.filter((item) => !item.busy);

            this.timeOptions = this.daySchedule.map((item) => `${item.bTime} - ${item.eTime}`);
            this.isDayDisabled = !!this.timeOptions.length;
        },
        pastDateValidation() {
            return isDateInPast(this.model.date) ? this.model.date : (this.model.date = '', this.isDayDisabled = null);
        },
        validateTimePeriod() {
            if (this.selectedTime.length <= 1) return;

            this.selectedTime.sort((a, b) => {
                const [bTime] = a.split('-');
                const [, eTime] = b.split('-');

                return parseInt(bTime) - parseInt(eTime);
            });

            for (let i = 0; i < this.selectedTime.length; i++) {
                const [curBTime, curETime] = this.selectedTime[i].split('-');
                const [nextBTime] = !!this.selectedTime[i + 1] ? this.selectedTime[i + 1].split('-') : '';
                const [, prevETime] = !!this.selectedTime[i - 1] ? this.selectedTime[i - 1].split('-') : '';

                const isPrev = !!prevETime && (curBTime.trim() === prevETime.trim());
                const isNext = !!nextBTime && (curETime.trim() === nextBTime.trim());

                if (!isPrev && !isNext) {
                    error('Можно выбрать несколько временных отрезков, только если они идут подряд!');
                    this.selectedTime = null;
                    break;
                }
            };
        },
        isFormInvalid() {
            return this.$refs.form.validate();
        },
        async save() {
            const isValid = await this.isFormInvalid();

            if (!isValid) return;

            const dataToServer = {
                employee: this.model.employee.id,
                communication: this.model.communication,
                clientTarget: this.model.clientTarget,
                clientComment: this.model.clientComment,
                employeeComment: this.model.employeeComment,
            };

            if (typeof this.model.status === 'string') {
                dataToServer.status = this.statusOptions.find((item) => item.label === this.model.status).value;
            } else {
                dataToServer.status = this.model.status.value;
            }

            if (this.selectedTime[0]) {
                const newDate = this.$moment(this.model.date).format('DD.MM.YYYY');
                const newTime = this.selectedTime[0].slice(0, 5);
                dataToServer.startTime = `${newDate} ${newTime}:00`;
            } else {
                dataToServer.startTime = this.model.startTime;
            }

            try {
                await this.editOrderData({id: this.id, payload: dataToServer});
                this.$router.push({name: 'OrderList'});
            } catch (e) {
                error('Что-то пошло не так, попробуйте позднее!');
            }
        },
    },
};
</script>
