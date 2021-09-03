<template>
    <div v-if="model" class="payment-details">
        <q-breadcrumbs class="q-mb-xl payment-details__breadcrumbs">
            <q-breadcrumbs-el
                icon="home"
                label="Панель Администратора"
                :to="{name: 'Main'}"
            />
            <q-breadcrumbs-el
                icon="widgets"
                :label="$loc('paymentNameMultiple')"
                :to="{name: 'PaymentList'}"
            />
            <q-breadcrumbs-el
                icon="face"
                class="payment-details__breadcrumbs payment-details__breadcrumbs_elStyle"
                :label="`Информация о ${$loc('paymentNameSingle')}`"
            />
        </q-breadcrumbs>

        <q-list
            class="payment-details__list"
            bordered
            separator
        >
            <q-item>
                <q-item-section>
                    <div class="row items-center">
                        <q-icon name="perm_identity" color="primary" size="sm" />
                        <span class="q-ml-sm">{{$loc('clientNameSingle')}}</span>
                    </div>
                </q-item-section>
                <q-item-section side>{{ model.clientName }}</q-item-section>
            </q-item>
            <q-item>
                <q-item-section>
                    <div class="row items-center">
                        <q-icon name="perm_identity" color="primary" size="sm" />
                        <span class="q-ml-sm">{{$loc('employeeNameSingle')}}</span>
                    </div>
                </q-item-section>
                <q-item-section side>{{ model.employeeName }}</q-item-section>
            </q-item>
            <q-item>
                <q-item-section>
                    <div class="row items-center">
                        <q-icon name="face" color="primary" size="sm" />
                        <span class="q-ml-sm">Заказ</span>
                    </div>
                </q-item-section>
                <q-item-section side>{{ model.orderId }}</q-item-section>
            </q-item>
            <q-item>
                <q-item-section>
                    <div class="row items-center">
                        <q-icon name="calendar_today" color="primary" size="sm" />
                        <span class="q-ml-sm">Дата создания</span>
                    </div>
                </q-item-section>
                <q-item-section side>{{ model.createTime }}</q-item-section>
            </q-item>
            <q-item>
                <q-item-section>
                    <div class="row items-center">
                        <q-icon name="calendar_today" color="primary" size="sm" />
                        <span class="q-ml-sm">Дата обновления</span>
                    </div>
                </q-item-section>
                <q-item-section side>{{ model.updateTime }}</q-item-section>
            </q-item>
            <q-item>
                <q-item-section>
                    <div class="row items-center">
                        <q-icon name="credit_card" color="primary" size="sm" />
                        <span class="q-ml-sm">Стоимость</span>
                    </div>
                </q-item-section>
                <q-item-section side>{{ currencyFormatter(model.amount) }}</q-item-section>
            </q-item>
            <q-item>
                <q-item-section>
                    <div class="row items-center">
                        <q-icon name="credit_card" color="primary" size="sm" />
                        <span class="q-ml-sm">Статус</span>
                    </div>
                </q-item-section>
                <q-item-section side>{{ getStatusFromStatusMap(model.status) }}</q-item-section>
            </q-item>
        </q-list>
    </div>
</template>

<script>
import {mapActions} from 'vuex';
import {currencyFormatter} from '../../../../common/utils/currencyFormatter';
import {getStatusFromStatusMap} from '../../../../common/utils/getStatusFromStatusMap';

export default {
    name: 'PaymentDetails',
    props: {
        id: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            model: null,
        };
    },
    mounted() {
        this.updatePaymentData();
    },
    methods: {
        getStatusFromStatusMap,
        currencyFormatter,
        ...mapActions({
            getPaymentDetails: 'payment/getPaymentDetails',
        }),
        async updatePaymentData() {
            const {data} = await this.getPaymentDetails(this.id);
            this.model = data;
        },
    },
};
</script>
