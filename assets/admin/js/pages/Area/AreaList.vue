<template>
    <div>
        <div class="row no-wrap justify-between items-center">
            <h1 class="text-h4 text-primary">
                {{$loc('areaList')}}
            </h1>
        </div>
        <q-table
            :data="tableData"
            :columns="tableColumns"
            :pagination.sync="pagination"
            :loading="loading"
            :filter="filter"
            :no-results-label="$loc('areaEmptyList')"
            binary-state-sort
            :rows-per-page-options="[0]"
            @request="fetchAreas"
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
                        <q-icon name="search" />
                    </template>
                </q-input>
            </template>
        </q-table>
    </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';

export default {
    name: 'AreaList',
    data() {
        return {
            tableColumns: [
                {name: 'name', align: 'left', label: 'Наименование', field: 'name'},
                {name: 'address', align: 'left', label: 'Адрес', field: 'address'},
            ],
            filter: '',
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
            areasData: 'area/areasData',
            areaRequestParams: 'area/areaRequestParams',
        }),
        tableData() {
            return this.areasData?.items || [];
        },
    },
    mounted() {
        this.fetchAreas({
            pagination: this.pagination,
            filter: null,
        });
    },
    methods: {
        ...mapActions({
            updateAreaRequestParams: 'area/updateAreaRequestParams',
        }),
        fetchAreas({pagination, filter}) {
            this.loading = true;
            this.updateAreaRequestParams({...pagination, filter})
                .then(() => {
                    this.pagination.rowsNumber = this.areasData.total;
                    this.pagination.rowsPerPage = this.areasData.limit;
                    this.pagination.page = this.areasData.page;
                    this.pagination.sortBy = pagination.sortBy;
                    this.pagination.descending = pagination.descending;
                    this.loading = false;
                });
        },
    },
};
</script>
