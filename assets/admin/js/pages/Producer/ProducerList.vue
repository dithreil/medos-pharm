<template>
    <div class="page-bottom-margin">
        <div class="row no-wrap justify-between items-center">
            <h1 class="text-h4 text-primary">
                Производители
            </h1>
            <q-btn
                flat
                color="primary"
                label="Создать производителя"
                @click="showProducerCreateModal"
            />
        </div>
        <q-table
            :data="tableData"
            :columns="tableColumns"
            :pagination.sync="pagination"
            :loading="loading"
            :filter="search"
            no-results-label="Список производителей пуст"
            binary-state-sort
            @request="fetchProducers"
        >
            <template #top-right>
                <q-input
                    v-model="search"
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
                            color="primary"
                            icon="edit"
                            title="Редактировать производителя"
                            @click="showProducerEditModal(props.row)"
                        />
                    </div>
                </q-td>
            </template>

        </q-table>
    </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import ProducerEdit from './ProducerEdit';
import ProducerCreate from './ProducerCreate';

export default {
    name: 'ProducerList',
    data() {
        return {
            tableColumns: [
                {name: 'fullName', align: 'left', label: 'Полное наименование', field: 'fullName', sortable: true},
                {name: 'shortName', align: 'left', label: 'Сокращенно', field: 'shortName'},
                {name: 'country', align: 'left', label: 'Страна', field: 'country'},
                {name: 'actions', align: 'left', label: 'Действия'},
            ],
            search: '',
            pagination: {
                sortBy: 'name',
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
            producersData: 'producer/producersData',
            producerRequestParams: 'producer/producerRequestParams',
        }),
        tableData() {
            return this.producersData?.items || [];
        },
    },
    mounted() {
        this.fetchProducers({
            pagination: this.pagination,
            filter: null,
        });
    },
    methods: {
        ...mapActions({
            updateProducerRequestParams: 'producer/updateProducerRequestParams',
        }),
        async fetchProducers({pagination}) {
            try {
                this.loading = true;

                pagination.limit = pagination.rowsPerPage;
                pagination.rowsPerPage = null;

                await this.updateProducerRequestParams({
                    pagination,
                    filter: this.search,
                });
                this.pagination.rowsNumber = this.producersData.total;
                this.pagination.rowsPerPage = this.producersData.limit;
                this.pagination.page = this.producersData.page;
                this.pagination.sortBy = pagination.sortBy;
                this.pagination.descending = pagination.descending;
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
        getTimeZonesTitle(zone) {
            const foundTimeZone = this.timeZones.find((timeZone) => timeZone.offset === zone);

            return foundTimeZone ? foundTimeZone.text : '';
        },
        showProducerEditModal(producer) {
            this.$q.dialog({
                component: ProducerEdit,
                parent: this,
                producer,
            });
        },
        showProducerCreateModal() {
            this.$q.dialog({
                component: ProducerCreate,
                parent: this,
            });
        },
    },
};
</script>
