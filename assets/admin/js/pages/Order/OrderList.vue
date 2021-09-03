<template>
    <div>
        <div class="row no-wrap justify-between items-center">
            <h1 class="text-h4 text-primary">
                {{$loc('orderList')}}
            </h1>
            <q-btn
                flat
                color="primary"
                :label="$loc('orderCreate')"
                :to="{name: 'OrderCreate'}"
            />
        </div>

        <q-table
            :data="tableData"
            :columns="tableColumns"
            row-key="id"
            :pagination.sync="pagination"
            :loading="loading"
            :filter="filter"
            :no-results-label="$loc('orderEmptyList')"
            binary-state-sort
            @request="fetchOrders"
        >
            <template #top-right>
                <q-input
                    v-model.trim="filter"
                    dense
                    debounce="300"
                    placeholder="Поиск"
                    style="min-width: 300px;"
                >
                    <template #prepend>
                        <q-icon name="search" />
                    </template>
                </q-input>
            </template>
            <template #body-cell-actions="props">
                <q-td :props="props">
                    <div class="row items-start q-gutter-xs">
                        <q-btn
                            dense
                            flat
                            color="grey"
                            title="Подробнее"
                            icon="info"
                            :to="{name: 'OrderDetails', params: {id: props.row.id}}"
                        />
                    </div>
                </q-td>
            </template>
        </q-table>
    </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';

export default {
    name: 'OrderList',
    data() {
        return {
            tableColumns: [
                {name: 'client', align: 'left', label: this.$loc('clientNameSingle'),
                    field: (row) => row.client.fullName, sortable: true},
                {name: 'employee', align: 'left', label: this.$loc('employeeNameSingle'),
                    field: (row) => row.employee.fullName, sortable: true},
                {name: 'createTime', align: 'left', label: 'Дата создания', field: 'createTime'},
                {name: 'updateTime', align: 'left', label: 'Дата обновления', field: 'updateTime'},
                {name: 'startTime', align: 'left', label: this.$loc('orderDateTime'), field: 'startTime'},
                {name: 'paymentTime', align: 'left', label: 'Дата оплаты', field: 'paymentTime'},
                {name: 'cost', align: 'left', label: 'Стоимость', field: 'cost'},
                {name: 'actions', align: 'left', label: 'Действия', field: 'actions'},
            ],
            filter: '',
            pagination: {
                sortBy: 'startTime',
                descending: false,
                page: 1,
                rowsPerPage: 10,
                rowsNumber: 0,
            },
            loading: false,
        };
    },
    computed: {
        ...mapGetters({
            ordersData: 'order/ordersData',
            orderRequestParams: 'order/orderRequestParams',
        }),
        tableData() {
            if (this.ordersData) {
                return this.ordersData.items;
            }

            return [];
        },
    },
    mounted() {
        this.fetchOrders({
            pagination: this.pagination,
            filter: null,
        });
    },
    methods: {
        ...mapActions({
            getOrdersList: 'order/getOrderList',
            updateOrderRequestParams: 'order/updateOrderRequestParams',
        }),
        fetchOrders({pagination, filter}) {
            this.loading = true;
            this.updateOrderRequestParams({
                ...this.$changePaginationKeys(pagination, this.ordersData?.total),
                filter,
            })
                .then(() => {
                    this.pagination.rowsNumber = this.ordersData.total;
                    this.pagination.rowsPerPage = this.ordersData.limit;
                    this.pagination.page = this.ordersData.page;
                    this.pagination.sortBy = pagination.sortBy;
                    this.pagination.descending = pagination.descending;
                    this.loading = false;
                });
        },
    },
};
</script>
