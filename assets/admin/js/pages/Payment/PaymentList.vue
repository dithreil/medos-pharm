<template>
    <div>
        <div class="row no-wrap justify-between items-center">
            <h1 class="text-h4 text-primary">
                {{$loc('paymentList')}}
            </h1>
        </div>

        <q-table
            :data="tableData"
            :columns="tableColumns"
            row-key="id"
            :pagination.sync="pagination"
            :loading="loading"
            :filter="filter"
            :no-results-label="$loc('paymentEmptyList')"
            binary-state-sort
            @request="fetchPayments"
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
                            :to="{name: 'PaymentDetails', params: {id: props.row.id}}"
                        />
                    </div>
                </q-td>
            </template>
        </q-table>
    </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import {currencyFormatter} from '../../../../common/utils/currencyFormatter';

export default {
    name: 'PaymentList',
    data() {
        return {
            tableColumns: [
                {name: 'clientName', align: 'left', label: this.$loc('clientNameSingle'),
                    field: 'clientName', sortable: true},
                {name: 'employeeName', align: 'left', label: this.$loc('employeeNameSingle'),
                    field: 'employeeName', sortable: true},
                {name: 'createTime', align: 'left', label: 'Дата создания', field: 'createTime'},
                {name: 'updateTime', align: 'left', label: 'Дата обновления', field: 'updateTime'},
                {name: 'amount', align: 'left', label: 'Стоимость',
                    field: (row) => (currencyFormatter(row.amount))},
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
            paymentData: 'payment/paymentData',
        }),
        tableData() {
            if (this.paymentData) {
                return this.paymentData.items;
            }

            return [];
        },
    },
    mounted() {
        this.fetchPayments({
            pagination: this.pagination,
            filter: null,
        });
    },
    methods: {
        ...mapActions({
            updatePaymentRequestParams: 'payment/updatePaymentRequestParams',
        }),
        fetchPayments({pagination, filter}) {
            if (pagination.rowsPerPage === 0) pagination.rowsPerPage = pagination.rowsNumber;
            this.loading = true;
            this.updatePaymentRequestParams({...pagination, filter})
                .then(() => {
                    this.pagination.rowsNumber = this.paymentData.total;
                    this.pagination.rowsPerPage = this.paymentData.limit;
                    this.pagination.page = this.paymentData.page;
                    this.pagination.sortBy = pagination.sortBy;
                    this.pagination.descending = pagination.descending;
                    this.loading = false;
                });
        },
    },
};
</script>
