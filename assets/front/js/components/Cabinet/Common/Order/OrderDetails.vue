<template>
    <q-card
        flat
        bordered
        class="my-card bg-grey-3"
        style="margin-bottom: 20px"
        v-if="order"
    >
        <div class="card-header">
            <h4 class="card-header card-header_title">
                Консультация на {{ order.startTime }}
            </h4>
            <q-separator />
        </div>
        <q-list
            class="employee-details__list"
            bordered
            separator
        >
            <q-item v-if="!isEmployee">
                <q-item-section>
                    <div class="row items-center">
                        <q-icon name="face" color="primary" size="sm" />
                        <span class="q-ml-sm">Врач</span>
                    </div>
                </q-item-section>
                <q-item-section side>{{ order.employee.fullName }}</q-item-section>
            </q-item>
            <q-item v-else>
                <q-item-section>
                    <div class="row items-center">
                        <q-icon name="face" color="primary" size="sm" />
                        <span class="q-ml-sm">Пациент</span>
                    </div>
                </q-item-section>
                <q-item-section side>{{ order.client.fullName }}</q-item-section>
            </q-item>
            <q-item>
                <q-item-section>
                    <div class="row items-center">
                        <q-icon name="event" color="primary" size="sm" />
                        <span class="q-ml-sm">Дата консультации</span>
                    </div>
                </q-item-section>
                <q-item-section side>{{ order.startTime }}</q-item-section>
            </q-item>
            <q-item>
                <q-item-section>
                    <div class="row items-center">
                        <q-icon name="date_range" color="primary" size="sm" />
                        <span class="q-ml-sm">Дата оплаты</span>
                    </div>
                </q-item-section>
                <q-item-section side>{{ order.paymentTime || 'Не оплачено'}}</q-item-section>
            </q-item>
            <q-item>
                <q-item-section>
                    <div class="row items-center">
                        <q-icon name="credit_card" color="primary" size="sm" />
                        <span class="q-ml-sm">Стоимость</span>
                    </div>
                </q-item-section>
                <q-item-section side>{{ order.cost }}</q-item-section>
            </q-item>
            <q-item>
                <q-item-section>
                    <div class="row items-center">
                        <q-icon name="credit_card" color="primary" size="sm" />
                        <span class="q-ml-sm">Статус</span>
                    </div>
                </q-item-section>
                <q-item-section side>{{ order.status }}</q-item-section>
            </q-item>
        </q-list>
        <div style="margin-top: 20px" >
            <h5 class="card-header card-header_title">
                Работа с файлами
            </h5>
            <OrderDocuments
                :orderDocuments="tableData"
                :orderId="id"
                @update:order="updateOrderData"
            />
            <div v-if="order" style="margin-top: 30px">
                <div v-if="isEmployee">
                    Место для заключения врача
                </div>
                <div v-else>
                    <ClientOrderReviewCard v-if="order.rating" :order="order" />
                    <ClientOrderReviewCreate v-else :orderId="id" />
                </div>
            </div>
        </div>
    </q-card>
</template>

<script>
import {mapActions} from 'vuex';
import ClientOrderReviewCreate from '../../Client/Order/Review/ClientOrderReviewCreate';
import ClientOrderReviewCard from '../../Client/Order/Review/ClientOrderReviewCard';
import {roleIdentifierMixin} from '../../../../mixins/roleIdentifierMixin';
import OrderDocuments from '../../../../../../common/UI/OrderDocuments/OrderDocuments.vue';

export default {
    name: 'OrderDetails',
    components: {OrderDocuments, ClientOrderReviewCard, ClientOrderReviewCreate},
    mixins: [roleIdentifierMixin],
    props: {
        id: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            order: {
                client: {fullName: ''},
                employee: {fullName: ''},
                startTime: '',
                paymentTime: '',
                cost: '',
                documents: [],
            },
            documents: null,
            documentsColumns: [
                {name: 'fileName', align: 'left', label: 'Наименование', field: 'fileName'},
                {name: 'createTime', align: 'left', label: 'Время добавления', field: 'createTime'},
            ],
            readyToCancel: true,
            dataToCancel: {
                action: 'action.cancel',
                rating: null,
                ratingComment: null,
            },
        };
    },
    computed: {
        tableData() {
            return this.order?.documents ?? [];
        },
    },
    async mounted() {
        await this.updateOrderData();
    },
    methods: {
        ...mapActions({
            getOrderDetails: 'order/getOrderDetails',
            uploadOrderDocuments: 'order/uploadOrderDocuments',
            orderSpecialActions: 'order/orderSpecialActions',
        }),
        cancel() {
            this.readyToCancel = false;
            this.orderSpecialActions({id: this.order.id, payload: this.dataToCancel}).then(() => {
                this.readyToCancel = true;
            });
        },
        async updateOrderData() {
            const {data} = await this.getOrderDetails(this.id);
            this.order = data;
        },
    },
};
</script>

