<template>
    <div>
        <div class="row no-wrap justify-between items-center">
            <h1 class="text-h4 text-primary">
                {{$loc('specialityList')}}
            </h1>
        </div>
        <q-table
            row-key="id"
            :data="tableData"
            :columns="tableColumns"
            :pagination.sync="pagination"
            :loading="loading"
            :filter="filter"
            :no-results-label="$loc('specialityEmptyList')"
            binary-state-sort
            @request="fetchCategories"
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

        </q-table>
    </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';

export default {
    name: 'SpecialityList',
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
            specialitiesData: 'speciality/specialitiesData',
            specialityRequestParams: 'speciality/specialityRequestParams',
        }),
        tableData() {
            if (this.specialitiesData) {
                return this.specialitiesData.items;
            }

            return [];
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
            updateSpecialityRequestParams: 'speciality/updateSpecialityRequestParams',
        }),
        fetchCategories({pagination, filter}) {
            if (pagination.rowsPerPage === 0) pagination.rowsPerPage = pagination.rowsNumber;
            this.loading = true;
            this.updateSpecialityRequestParams({...pagination, filter})
                .then(() => {
                    this.pagination.rowsNumber = this.specialitiesData.total;
                    this.pagination.rowsPerPage = this.specialitiesData.limit;
                    this.pagination.page = this.specialitiesData.page;
                    this.pagination.sortBy = pagination.sortBy;
                    this.pagination.descending = pagination.descending;
                    this.loading = false;
                });
        },
    },
};
</script>
