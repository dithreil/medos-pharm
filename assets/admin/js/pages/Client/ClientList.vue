<template>
    <div>
        <div class="row no-wrap justify-between items-center">
            <h1 class="text-h4 text-primary">
                {{$loc('clientList')}}
            </h1>
            <q-btn
                flat
                color="primary"
                :label="$loc('clientCreate')"
                :to="{name: 'ClientCreate'}"
            />
        </div>

        <q-table
            :data="tableData"
            :columns="tableColumns"
            row-key="id"
            :pagination.sync="pagination"
            :loading="loading"
            :filter="filter"
            :no-data-label="$loc('clientEmptyList')"
            binary-state-sort
            @request="fetchUsers"
        >
            <template #top-left>
                <q-toggle
                    :value="userRequestParams.active"
                    label="Показать только активных пользователей"
                    @input="toggleActiveUsers"
                />
            </template>

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

            <template #body-cell-email="props">
                <q-td :props="props">
                    <q-btn
                        class="stylesBtn"
                        dense
                        flat
                        :label="props.row.email"
                        :to="{name: 'ClientDetails', params: {id: props.row.id}}"
                    />
                </q-td>
            </template>

            <template #body-cell-firstName="props">
                <q-td :props="props">
                    <q-btn
                        class="stylesBtn"
                        dense
                        flat
                        :label="props.row.fullName"
                        :to="{name: 'ClientDetails', params: {id: props.row.id}}"
                    />
                </q-td>
            </template>

            <template #body-cell-phoneNumber="props">
                <q-td :props="props">
                    <q-btn
                        class="stylesBtn"
                        dense
                        flat
                        :label="props.row.phoneNumber | phoneFilter"
                        :to="{name: 'ClientDetails', params: {id: props.row.id}}"
                    />
                </q-td>
            </template>

            <template #body-cell-snils="props">
                <q-td
                    class="stylesBtn"
                    :props="props">
                    {{ props.row.snils | snilsFilter }}
                </q-td>
            </template>

            <template #body-cell-blockuser="props">
                <q-td :props="props">
                    <div class="row items-start q-gutter-xs">
                        <q-btn
                            dense
                            flat
                            :color="!props.row.isActive ? 'positive' : 'negative'"
                            :icon="!props.row.isActive ? 'lock_open' : 'lock'"
                            :title="props.row.isActive ? 'Блокировать' : 'Разблокировать'"
                            @click="confirmBlockUser(props.row.id, props.row.email, props.row.isActive)"
                        />
                        <q-btn
                            dense
                            flat
                            color="primary"
                            icon="edit"
                            title="Редактировать"
                            :to="{name: 'ClientEdit', params:{id: props.row.id}}"
                        />
                        <q-btn
                            dense
                            flat
                            color="primary"
                            icon="password"
                            title="Изменить пароль"
                            @click="showPasswordModal(props.row.id)"
                        />
                    </div>
                </q-td>
            </template>
        </q-table>

        <ClientChangePassword ref="changePasswordModal" />
    </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import ClientChangePassword from './ClientChangePassword.vue';
import {phoneFilter, snilsFilter} from '../../filters';

export default {
    name: 'ClientList',
    filters: {phoneFilter, snilsFilter},
    components: {ClientChangePassword},
    data() {
        return {
            tableColumns: [
                {name: 'email', align: 'left', label: 'Email', field: 'email', sortable: true},
                {name: 'firstName', align: 'left', label: 'Имя', field: 'fullName', sortable: true},
                {name: 'phoneNumber', align: 'left', label: 'Телефон', field: 'phoneNumber'},
                {name: 'snils', align: 'left', label: 'СНИЛС', field: 'snils'},
                {name: 'blockuser', align: 'left', label: 'Действия', field: ''},
            ],
            filter: '',
            onlyActiveUsers: false,
            pagination: {
                sortBy: 'email',
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
            usersData: 'client/usersData',
            userRequestParams: 'client/userRequestParams',
        }),
        tableData() {
            return this.usersData?.items || [];
        },
    },
    mounted() {
        this.fetchUsers({
            pagination: this.pagination,
            filter: null,
        });
    },
    methods: {
        ...mapActions({
            getUsersList: 'client/getUsersList',
            toggleBlockUser: 'client/toggleBlockUser',
            updateUserRequestParams: 'client/updateUserRequestParams',
        }),
        fetchUsers({pagination, filter}) {
            this.loading = true;
            this.updateUserRequestParams({
                ...this.$changePaginationKeys(pagination, this.usersData?.total),
                filter,
            })
                .then(() => {
                    this.pagination.rowsNumber = this.usersData.total;
                    this.pagination.rowsPerPage = this.usersData.limit;
                    this.pagination.page = this.usersData.page;
                    this.pagination.sortBy = pagination.sortBy;
                    this.pagination.descending = pagination.descending;
                    this.loading = false;
                });
        },
        confirmBlockUser(id, name, isActive) {
            const action = isActive ? 'заблокировать' : 'разблокировать';
            this.$q.dialog({
                title: 'Блокировать',
                message: `Вы действительно хотите ${action} пользователя ${name}?`,
                ok: {
                    label: 'Да',
                },
                cancel: {
                    color: 'negative',
                    label: 'Отмена',
                },
                persistent: true,
            }).onOk(() => {
                this.toggleBlockUser(id);
            });
        },
        toggleActiveUsers(active) {
            this.loading = true;
            this.updateUserRequestParams({active})
                .then(() => {
                    this.pagination.rowsNumber = this.usersData.total;
                    this.pagination.rowsPerPage = this.usersData.limit;
                    this.pagination.page = this.usersData.page;
                    this.loading = false;
                });
        },
        showPasswordModal(id) {
            this.$refs.changePasswordModal.toggleModalActivity(id);
        },
        showUserCreateModal(userData) {
            this.$refs.userCreateModal.toggleModalActivity();
        },
    },
};
</script>
