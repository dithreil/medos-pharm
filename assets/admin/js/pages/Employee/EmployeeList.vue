<template>
    <div>
        <div class="row no-wrap justify-between items-center">
            <h1 class="text-h4 text-primary">
                {{$loc('employeeList')}}
            </h1>
        </div>

        <q-table
            :data="tableData"
            :columns="tableColumns"
            row-key="id"
            :pagination.sync="pagination"
            :loading="loading"
            :filter="filter"
            :no-data-label="$loc('employeeEmptyList')"
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
                        :to="{name: 'EmployeeDetails', params: {id: props.row.id}}"
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
                        :to="{name: 'EmployeeDetails', params: {id: props.row.id}}"
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
                        :to="{name: 'EmployeeDetails', params: {id: props.row.id}}"
                    />
                </q-td>
            </template>

            <template #body-cell-specialization="props">
                <q-td :props="props">
                    <q-btn
                        class="stylesBtn"
                        dense
                        flat
                        :label="props.row.speciality"
                        :to="{name: 'EmployeeDetails', params: {id: props.row.id}}"
                    />
                </q-td>
            </template>

            <template #body-cell-blockuser="props">
                <q-td :props="props">
                    <div class="row items-start q-gutter-xs">
                        <q-btn
                            dense
                            flat
                            :color="props.row.isActive ? 'negative' : 'positive'"
                            :icon="props.row.isActive ? 'lock' : 'lock_open'"
                            :title="props.row.isActive ? 'Блокировать' : 'Разблокировать'"
                            @click="confirmBlockUser(props.row.id, props.row.email, props.row.isActive)"
                        />
                    </div>
                </q-td>
            </template>
        </q-table>

        <EmployeeChangePassword ref="changePasswordModal" />
    </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import EmployeeChangePassword from './EmployeeChangePassword.vue';
import {phoneFilter} from '../../filters';

export default {
    name: 'EmployeeList',
    filters: {phoneFilter},
    components: {EmployeeChangePassword},
    data() {
        return {
            tableColumns: [
                {name: 'email', align: 'left', label: 'Email', field: 'email', sortable: true},
                {name: 'firstName', align: 'left', label: 'Имя', field: 'fullName', sortable: true},
                {name: 'phoneNumber', align: 'left', label: 'Телефон', field: 'phoneNumber'},
                {name: 'speciality', align: 'left', label: 'Специализация', field: 'speciality'},
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
            usersData: 'employee/usersData',
            userRequestParams: 'employee/userRequestParams',
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
            getUsersList: 'employee/getUsersList',
            toggleBlockUser: 'employee/toggleBlockUser',
            updateUserRequestParams: 'employee/updateUserRequestParams',
        }),
        fetchUsers({pagination, filter}) {
            if (pagination.rowsPerPage === 0) pagination.rowsPerPage = pagination.rowsNumber;
            this.loading = true;
            this.updateUserRequestParams({...pagination, filter})
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
    },
};
</script>
