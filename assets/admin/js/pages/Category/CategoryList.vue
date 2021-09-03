<template>
    <div>
        <div class="row no-wrap justify-between items-center">
            <h1 class="text-h4 text-primary">
                {{$loc('categoryList')}}
            </h1>
        </div>
        <q-table
            :data="tableData"
            :columns="tableColumns"
            :pagination.sync="pagination"
            :loading="loading"
            :filter="filter"
            :no-results-label="$loc('categoryEmptyList')"
            :rows-per-page-options="[0]"
            binary-state-sort
            @request="fetchCategories"
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
    name: 'CategoryList',
    data() {
        return {
            tableColumns: [
                {name: 'name', align: 'left', label: 'Наименование', field: 'name'},
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
            categoriesData: 'category/categoriesData',
            categoryRequestParams: 'category/categoryRequestParams',
        }),
        tableData() {
            return this.categoriesData?.items || [];
        },
    },
    mounted() {
        this.fetchCategories({
            pagination: this.pagination,
            filter: null,
        });
    },
    methods: {
        ...mapActions({
            updateCategoryRequestParams: 'category/updateCategoryRequestParams',
        }),
        fetchCategories({pagination, filter}) {
            this.loading = true;
            this.updateCategoryRequestParams({...pagination, filter})
                .then(() => {
                    this.pagination.rowsNumber = this.categoriesData.total;
                    this.pagination.rowsPerPage = this.categoriesData.limit;
                    this.pagination.page = this.categoriesData.page;
                    this.pagination.sortBy = pagination.sortBy;
                    this.pagination.descending = pagination.descending;
                    this.loading = false;
                });
        },
    },
};
</script>
