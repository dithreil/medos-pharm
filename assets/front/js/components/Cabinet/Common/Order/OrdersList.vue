<template>
    <q-card
        flat
        bordered
        class="my-card bg-grey-3"
    >
        <div class="card-header">
            <h4 class="card-header card-header_title">
                Мои консультации
            </h4>
            <q-separator />
        </div>
        <q-table
            style="margin-top: 20px"
            :data="ordersData ? ordersData.items : []"
            :columns="ordersTableColumns"
            row-key="id"
            binary-state-sort
            :pagination.sync="pagination"
            :loading="loading"
            no-data-label="У вас еще нет консультаций"
            :filter="filter"
            @request="fetchOrders"
        >
            <template #top-right>
                <q-select
                    v-model="filter"
                    :options="filteredOptions"
                    filled
                    map-options
                    emit-value
                    behavior="menu"
                />
            </template>
            <template #body-cell-paymentTime="props">
                <q-td :props="props">

                    <div class="row items-start q-gutter-xs">
                        <div v-if="props.row.paymentTime"> {{ props.row.paymentTime }}</div>
                        <div v-else>Еще не оплачено</div>
                    </div>
                </q-td>
            </template>
            <template #body-cell-blockuser="props">
                <q-td :props="props">
                    <div class="row items-start q-gutter-xs">
                        <div v-if="!isEmployee">
                            <q-btn
                                v-if="isOrderClosable(props.row)"
                                label="Отменить"
                                dense
                                flat
                            >
                                <q-tooltip content-class="bg-purple" content-style="font-size: 16px" :offset="[10, 10]">
                                    Для отмены консультации позвоните по телефону :
                                    Ростов-на-Дону 8 (928) 905-84-62
                                    Батайск 8 (928) 902-03-03
                                    Таганрог 8(928) 777-44-19
                                    Новочеркасск 8 (928) 770-82-95
                                </q-tooltip>
                            </q-btn>
                            <q-btn
                                v-if="!props.row.paymentTime"
                                dense
                                flat
                                label="Оплатить"
                                @click="payTheOrder(props.row.id)"
                            />
                        </div>
                        <q-btn
                            dense
                            flat
                            color="grey"
                            title="Подробнее"
                            icon="info"
                            :to="{name: linkToDetails, params: {id: props.row.id}}"
                        />
                    </div>
                </q-td>
            </template>
        </q-table>
    </q-card>
</template>

<script>
import {mapGetters, mapActions} from 'vuex';
import {roleIdentifierMixin} from '../../../../mixins/roleIdentifierMixin';
import moment from 'moment';
import ClientOrderPayment from '../../Client/Order/ClientOrderPayment.vue';
import {error} from '../../../../utils/notifizer';

export default {
    name: 'OrdersList',
    mixins: [roleIdentifierMixin],
    data() {
        return {
            ordersTableColumns: [

                {name: 'startTime', align: 'left', label: 'Дата консультации', field: 'startTime'},
                {name: 'cost', align: 'left', label: 'Стоимость', field: 'cost'},
                {name: 'paymentTime', align: 'left', label: 'Дата оплаты', field: 'paymentTime'},
                {name: 'blockuser', align: 'left'}],
            pagination: {
                sortBy: 'startTime',
                descending: false,
                page: 1,
                rowsPerPage: 10,
                rowsNumber: 0,
            },
            filteredOptions: [
                {label: 'Все', value: ''},
                {label: 'Неоплаченные', value: 'notPaid'},
                {label: 'Оплаченные', value: 'forthcoming'},
                {label: 'Прошедшие', value: 'past'},
                {label: 'Неоценённые', value: 'notRated'},
            ],
            filter: '',
            loading: false,
        };
    },
    computed: {
        ...mapGetters({
            userData: 'user/userData',
            ordersData: 'order/ordersData',
        }),
        linkToDetails() {
            return this.isEmployee ? 'EmployeeOrderDetails' : 'ClientOrderDetails';
        },
    },
    mounted() {
        this.fetchOrders(this.pagination,);
        this.ordersTableColumns.unshift(this.isEmployee
            ? {name: 'client', align: 'left', label: 'Клиент', field: (row) => (row.client.fullName), sortable: true}
            : {name: 'employee', align: 'left', label: 'Врач', field: (row) => (row.employee.fullName), sortable: true}
        );
    },
    methods: {
        ...mapActions({
            updateOrderRequestParams: 'order/updateOrderRequestParams',
            createPayment: 'payment/createPayment',
        }),
        isOrderClosable(order) {
            if (!order.paymentTime) return true;

            return moment(order.paymentTime, 'DD.MM.YYYY HH:mm:SS').diff(moment(), 'days') > 0;
        },
        async payTheOrder(id) {
            this.$q.dialog({
                component: ClientOrderPayment,
                parent: this,
            }).onOk(async() => {
                try {
                    const {data} = await this.createPayment({order: id});

                    window.location.href = data.confirmationUrl;
                } catch (err) {
                    error('Что-то пошло не так!');
                }
            });
        },
        async fetchOrders({pagination, filter}) {
            this.loading = true;

            const dataToServer = {...pagination};
            if (this.filter) dataToServer[this.filter] = true;

            try {
                await this.updateOrderRequestParams(dataToServer);
                this.pagination.rowsNumber = this.ordersData.total;
                this.pagination.rowsPerPage = this.ordersData.limit;
                this.pagination.page = this.ordersData.page;
                this.pagination.sortBy = pagination.sortBy;
                this.pagination.descending = pagination.descending;
                this.loading = false;
            } catch (error) {
                this.loading = false;
                console.log(error);
            }
        },
    },
};
</script>
