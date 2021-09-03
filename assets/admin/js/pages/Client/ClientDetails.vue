<template>
    <div class="client-details">
        <q-breadcrumbs class="q-mb-xl client-details__breadcrumbs">
            <q-breadcrumbs-el>
                <a class="q-breadcrumbs__el q-link" href="/admin">Панель Администратора</a>
            </q-breadcrumbs-el>
            <q-breadcrumbs-el
                :label="$loc('clientNameMultiple')"
                :to="{name: 'ClientList'}"
            />
            <q-breadcrumbs-el
                class="employee-details__breadcrumbs client-details__breadcrumbs_elStyle"
                :label="`Информация о ${user.fullName}`"
            />
        </q-breadcrumbs>
        <q-list
            class="client-details__list"
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
                        <span class="q-ml-sm">{{$loc('clientFullName')}}</span>
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
            :to="{name: 'ClientEdit', params:{id}}"
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
            >
                <template #body-cell-paymentTime="props">
                    <q-td :props="props">{{ props.row.paymentTime || 'Не оплачен' }}</q-td>
                </template>
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
        <ClientChangePassword ref="changePasswordModal" />
    </div>
</template>

<script>
import {mapActions} from 'vuex';
import ClientChangePassword from './ClientChangePassword.vue';
export default {
    components: {ClientChangePassword},
    name: 'ClientDetails',
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
                {name: 'employee', align: 'left', label: 'Врач', field: (row) => row.employee.fullName, sortable: true},
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
            getUserDetails: 'client/getUserDetails',
        }),
        showPasswordModal(id) {
            this.$refs.changePasswordModal.toggleModalActivity(id);
        },
    },
};
</script>
