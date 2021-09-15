<template>
  <div class="page-bottom-margin">
    <div class="row no-wrap justify-between items-center">
      <h1 class="text-h4 text-primary">
        Список поступлений
      </h1>
      <q-btn
          flat
          color="primary"
          label="Создать поступление товаров"
          :to="{name: 'IncomeCreate'}"
      />
    </div>
    <q-table
        :data="tableData"
        :columns="tableColumns"
        :pagination.sync="pagination"
        :loading="loading"
        :filter="search"
        no-results-label="Список поступлений пуст"
        binary-state-sort
        @request="fetchIncomes"
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
            <q-icon name="search"/>
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
                title="Редактировать потсупление"
                :to="{name: 'IncomeEdit', params: {incomeId: props.row.id}}"
            />
            <q-btn
                dense
                flat
                color="grey"
                title="Подробнее"
                icon="info"
                :to="{name: 'IncomeDetails', params: {incomeId: props.row.id}}"
            />
          </div>
        </q-td>
      </template>
    </q-table>
  </div>
</template>

<script lang="ts">
import {mapActions, mapGetters} from 'vuex';
import {Component, Vue} from 'vue-property-decorator';
import {IPagination, IRequestParams} from '../../interfaces/request-params';
import {IDocumentIncome, IDocumentIncomeData} from '../../interfaces/income';


@Component({
    computed: {
        ...mapGetters({
            incomesData: 'income/incomesData',
            incomeRequestParams: 'income/incomeRequestParams',
        }),
    },
    methods: {
        ...mapActions({
            updateIncomeRequestParams: 'income/updateIncomeRequestParams',
        }),
    },
})
export default class IncomeList extends Vue {
  protected incomesData!: IDocumentIncomeData;
  protected updateIncomeRequestParams!: (payload: IRequestParams) => any;

  private tableColumns = [
      {name: 'name', align: 'left', label: 'Дата поступления', field: 'date', sortable: true},
      {name: 'supplier', align: 'left', label: 'Поставщик', field: (row: IDocumentIncome) => row.supplier.name},
      {name: 'store', align: 'left', label: 'Торговая точка', field: (row: IDocumentIncome) => row.store.name},
      {name: 'actions', align: 'left', label: 'Действия'},
  ];
  private search = '';
  private pagination: IPagination = {
      sortBy: 'date',
      descending: false,
      page: 1,
      rowsPerPage: 10,
      rowsNumber: 0,
      limit: null,
  };
  private loading = false;

  get tableData() {
      return this.incomesData?.items || [];
  };

  mounted() {
      this.fetchIncomes({pagination: this.pagination});
  };

  async fetchIncomes({pagination}: { pagination: IPagination }) {
      this.loading = true;

      pagination.limit = pagination.rowsPerPage;
      pagination.rowsPerPage = null;

      await this.updateIncomeRequestParams({
          ...pagination,
          filter: this.search,
      });
      this.pagination.rowsNumber = this.incomesData.total;
      this.pagination.rowsPerPage = this.incomesData.limit;
      this.pagination.page = this.incomesData.page;
      this.pagination.sortBy = pagination.sortBy;
      this.pagination.descending = pagination.descending;

      this.loading = false;
  };
};
</script>
