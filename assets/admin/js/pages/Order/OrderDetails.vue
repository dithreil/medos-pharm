<template>
    <div style="padding-bottom: 30px" v-if="order" class="employee-details">
        <q-breadcrumbs class="q-mb-xl employee-details__breadcrumbs">
            <q-breadcrumbs-el icon="home">
                <a class="q-breadcrumbs__el q-link" href="/admin">Панель Администратора</a>
            </q-breadcrumbs-el>
            <q-breadcrumbs-el
                icon="widgets"
                :label="$loc('orderMultiple')"
                :to="{name: 'OrderList'}"
            />
            <q-breadcrumbs-el
                icon="face"
                class="employee-details__breadcrumbs employee-details__breadcrumbs_elStyle"
                :label="`Информация о ${$loc('employeeNameSingle')}:
                ${order.employee.fullName}/${$loc('clientNameSingle')}:
                ${order.client.fullName} ${order.startTime}`"
                @click="$router.push({name: 'OrderList'})"
            />
        </q-breadcrumbs>

        <q-list
            class="employee-details__list"
            bordered
            separator
        >
            <q-item>
                <q-item-section>
                    <div class="row items-center">
                        <q-icon name="perm_identity" color="primary" size="sm"/>
                        <span class="q-ml-sm">{{ $loc('clientNameSingle') }}</span>
                    </div>
                </q-item-section>
                <q-item-section side>{{ order.client.fullName }}</q-item-section>
            </q-item>
            <q-item>
                <q-item-section>
                    <div class="row items-center">
                        <q-icon name="face" color="primary" size="sm"/>
                        <span class="q-ml-sm">{{ $loc('employeeNameSingle') }}</span>
                    </div>
                </q-item-section>
                <q-item-section side>{{ order.employee.fullName }}</q-item-section>
            </q-item>
            <q-item>
                <q-item-section>
                    <div class="row items-center">
                        <q-icon name="calendar_today" color="primary" size="sm"/>
                        <span class="q-ml-sm">Дата создания</span>
                    </div>
                </q-item-section>
                <q-item-section side>{{ order.createTime }}</q-item-section>
            </q-item>
            <q-item>
                <q-item-section>
                    <div class="row items-center">
                        <q-icon name="calendar_today" color="primary" size="sm"/>
                        <span class="q-ml-sm">Дата обновления</span>
                    </div>
                </q-item-section>
                <q-item-section side>{{ order.updateTime }}</q-item-section>
            </q-item>
            <q-item>
                <q-item-section>
                    <div class="row items-center">
                        <q-icon name="event" color="primary" size="sm"/>
                        <span class="q-ml-sm">{{ $loc('orderDateTime') }}</span>
                    </div>
                </q-item-section>
                <q-item-section side>{{ order.startTime }}</q-item-section>
            </q-item>
            <q-item>
                <q-item-section>
                    <div class="row items-center">
                        <q-icon name="date_range" color="primary" size="sm"/>
                        <span class="q-ml-sm">Дата оплаты</span>
                    </div>
                </q-item-section>
                <q-item-section side>{{ order.paymentTime }}</q-item-section>
            </q-item>
            <q-item>
                <q-item-section>
                    <div class="row items-center">
                        <q-icon name="credit_card" color="primary" size="sm"/>
                        <span class="q-ml-sm">Стоимость</span>
                    </div>
                </q-item-section>
                <q-item-section side>{{ order.cost }}</q-item-section>
            </q-item>
            <q-item>
                <q-item-section>
                    <div class="row items-center">
                        <q-icon name="credit_card" color="primary" size="sm"/>
                        <span class="q-ml-sm">Статус</span>
                    </div>
                </q-item-section>
                <q-item-section side>{{ order.status }}</q-item-section>
            </q-item>
        </q-list>
        <q-btn
            flat
            color="primary"
            label="Редактировать"
            @click="$router.push({name: 'OrderEdit', params: {id}})"
        />
        <div>
            <div class="row no-wrap justify-between items-center">
                <h1 class="text-h5 text-primary">
                    Список документов
                </h1>
            </div>
            <OrderDocuments
                style="max-width: 700px"
                :orderDocuments="tableData"
                :orderId="id"
                :fromAdmin="true"
                @update:order="updateOrderData"
            />
        </div>
        <div style="margin-bottom: 30px; max-width: 1000px">
            <div class="row no-wrap justify-between items-center">
                <h1 class="text-h5 text-primary">
                    Оплаты
                </h1>
            </div>

            <q-table
                :data="order.payments"
                class="employee-details employee-details_table"
                style="margin-bottom: 30px; max-width: 1000px"
                :columns="tableColumns"
                row-key="id"
                :pagination.sync="pagination"
                :loading="loading"
                :filter="filter"
                no-results-label="По запросу ничего не найдено"
                binary-state-sort
            >
                <template #top-right>
                    <q-input
                        v-model="filter"
                        dense
                        debounce="300"
                        placeholder="Поиск"
                        style="min-width: 300px;"
                    >
                        <template #prepend>
                            <q-icon name="search"/>
                        </template>
                    </q-input>
                </template>
            </q-table>
        </div>
        <div style="margin-bottom: 30px; max-width: 1000px">
            <div class="row no-wrap justify-between items-center">
                <h1 class="text-h5 text-primary">
                    История изменений
                </h1>
            </div>
            <q-table
                :data="order.history"
                class="employee-details employee-details_table"
                style="margin-bottom: 30px; max-width: 1000px"
                :columns="historyColumns"
                binary-state-sort
            >
                <template #top-right>
                    <q-input
                        v-model="filter"
                        dense
                        debounce="300"
                        placeholder="Поиск"
                        style="min-width: 300px;"
                    >
                        <template #prepend>
                            <q-icon name="search"/>
                        </template>
                    </q-input>
                </template>
            </q-table>
        </div>
    </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import {orderStatuses} from '../../../../common/constants';
import OrderDocuments from '../../../../common/UI/OrderDocuments/OrderDocuments.vue';
import {getStatusFromStatusMap} from '../../../../common/utils/getStatusFromStatusMap';

export default {
    name: 'OrderDetails',
    components: {OrderDocuments},
    props: {
        id: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            tableColumns: [
                {name: 'paymentTime', align: 'left', label: 'Дата оплаты', field: 'paymentTime'},
                {name: 'cost', align: 'left', label: 'Стоимость', field: 'cost'},
                {name: 'blockuser', align: 'left', label: 'Статус', field: ''},
            ],
            historyColumns: [
                {name: 'changeType', align: 'left', label: 'Тип изменения',
                    field: (row) => (getStatusFromStatusMap(row.changeType))},
                {name: 'newValue', align: 'left', label: 'Новое значение',
                    field: (row) => (getStatusFromStatusMap(row.newValue))},
                {name: 'who', align: 'left', label: 'Автор',
                    field: (row) => (row.who === 'client' ? 'Клиент' : 'Сотрудник')},
                {name: 'createTime', align: 'left', label: 'Время изменения', field: 'createTime'},
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
            order: {
                client: {fullName: ''},
                employee: {fullName: ''},
                createTime: '',
                updateTime: '',
                startTime: '',
                paymentTime: '',
                cost: '',
                status: '',
                documents: [],
            },
        };
    },
    computed: {
        ...mapGetters({
            ordersData: 'order/ordersData',
            orderRequestParams: 'order/orderRequestParams',
        }),
        tableData() {
            return this.ordersData?.items ?? [];
        },
    },
    mounted() {
        this.updateOrderData();
    },
    methods: {
        ...mapActions({
            getOrderDetails: 'order/getOrderDetails',
        }),
        async updateOrderData() {
            const {data} = await this.getOrderDetails(this.id);
            this.order = data;
            this.order.status = orderStatuses.find((item) => item.key === this.order.status).label;
        },
    },
};
</script>
