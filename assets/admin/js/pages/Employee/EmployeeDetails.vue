<template>
    <div v-if="user" class="employee-details">
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
                :label="`Информация о ${user.fullName}`"
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
                        <q-icon name="email" color="primary" size="sm" />
                        <span class="q-ml-sm">E-mail</span>
                    </div>
                </q-item-section>
                <q-item-section side>{{ user.email }}</q-item-section>
            </q-item>
            <q-item>
                <q-item-section>
                    <div class="row items-center">
                        <q-icon name="face" color="primary" size="sm" />
                        <span class="q-ml-sm">{{$loc('employeeFullName')}}</span>
                    </div>
                </q-item-section>
                <q-item-section side>{{ user.fullName }}</q-item-section>
            </q-item>
            <q-item>
                <q-item-section>
                    <div class="row items-center">
                        <q-icon name="phone" color="primary" size="sm" />
                        <span class="q-ml-sm">Телефон</span>
                    </div>
                </q-item-section>
                <q-item-section side>+7{{ user.phoneNumber }}</q-item-section>
            </q-item>
            <q-item>
                <q-item-section>
                    <div class="row items-center">
                        <q-icon name="done_outline" color="primary" size="sm" />
                        <span class="q-ml-sm">Активен</span>
                    </div>
                </q-item-section>
                <q-item-section v-if="user.isActive" side>Да</q-item-section>
                <q-item-section v-else side>Нет</q-item-section>
            </q-item>
        </q-list>
        <q-btn
            flat
            color="primary"
            label="Редактировать"
            :to="{name: 'EmployeeEdit', params:{userId: id}}"
        />
        <q-btn
            dense
            flat
            color="primary"
            title="Изменить пароль"
            label="Изменить пароль"
            @click="showPasswordModal(id)"
        />
        <q-card style="margin-top: 30px">
            <div class="row no-wrap justify-between items-center">
                <h1 class="text-h4 text-primary">
                    {{$loc('orderList')}}
                </h1>
            </div>

            <q-table
                :data="user.orders"
                :columns="ordersTableColumns"
                row-key="id"
                binary-state-sort
                @row-click="testRowClick"
            >
                <template #body-cell-blockUser="props">
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
        </q-card>
        <EmployeeChangePassword ref="changePasswordModal" />
    </div>
</template>


<script>
import EmployeeChangePassword from './EmployeeChangePassword.vue';
import {mapActions} from 'vuex';

export default {
    components: {EmployeeChangePassword},
    name: 'EmployeeDetails',
    props: {
        id: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            user: '',
            ordersTableColumns: [
                {name: 'client', align: 'left', label: 'Клиент', field: (row) => row.client.fullName, sortable: true},
                {name: 'createTime', align: 'left', label: 'Дата создания', field: 'createTime'},
                {name: 'updateTime', align: 'left', label: 'Дата обновления', field: 'updateTime'},
                {name: 'startTime', align: 'left', label: 'Дата консультации', field: 'startTime'},
                {name: 'paymentTime', align: 'left', label: 'Дата оплаты', field: 'paymentTime'},
                {name: 'cost', align: 'left', label: 'Стоимость', field: 'cost'},
                {name: 'blockUser', align: 'left', label: 'Подробнее'},
            ],
        };
    },
    mounted() {
        this.getUserDetails(this.id)
            .then((response) => {
                this.user = response.data;
            });
    },
    methods: {
        ...mapActions({
            getUserDetails: 'employee/getUserDetails',
        }),
        showPasswordModal(id) {
            this.$refs.changePasswordModal.toggleModalActivity(id);
        },
        testRowClick(e, row) {
            window.open(`/admin/order/${row.id}`);
        },
    },
};
</script>

