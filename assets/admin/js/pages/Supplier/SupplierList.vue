<template>
  <div class="page-bottom-margin">
    <div class="row no-wrap justify-between items-center">
      <h1 class="text-h4 text-primary">
        Поставщики
      </h1>
      <q-btn
          flat
          color="primary"
          label="Создать поставщика"
          @click="showSupplierCreateModal"
      />
    </div>
    <q-table
        :data="tableData"
        :columns="tableColumns"
        :pagination.sync="pagination"
        :loading="loading"
        :filter="search"
        no-results-label="Список поставщиков пуст"
        binary-state-sort
        @request="fetchSuppliers"
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
                title="Редактировать поставщика"
                @click="showSupplierEditModal(props.row)"
            />
          </div>
        </q-td>
      </template>

    </q-table>
  </div>
</template>

<script lang="ts">
import {mapActions, mapGetters} from 'vuex';
import SupplierEdit from './SupplierEdit.vue';
import SupplierCreate from './SupplierCreate.vue';
import {Component, Vue} from 'vue-property-decorator';
import {IPagination, IRequestParams} from '../../interfaces/request-params';
import {ISupplier, ISupplierData} from '../../interfaces/supplier';


@Component({
    computed: {
        ...mapGetters({
            suppliersData: 'supplier/suppliersData',
            supplierRequestParams: 'supplier/supplierRequestParams',
        }),
    },
    methods: {
        ...mapActions({
            updateSupplierRequestParams: 'supplier/updateSupplierRequestParams',
        }),
    },
})
export default class SupplierList extends Vue {
  protected suppliersData!: ISupplierData;
  protected updateSupplierRequestParams!: (payload: IRequestParams) => any;

  private tableColumns = [
      {name: 'name', align: 'left', label: 'Наименование', field: 'name', sortable: true},
      {name: 'address', align: 'left', label: 'Адрес', field: 'address'},
      {name: 'actions', align: 'left', label: 'Действия'},
  ];
  private search = '';
  private pagination: IPagination = {
      sortBy: 'name',
      descending: false,
      page: 1,
      rowsPerPage: 10,
      rowsNumber: 0,
      limit: null,
  };
  private loading = false;

  get tableData() {
      return this.suppliersData?.items || [];
  };

  mounted() {
      this.fetchSuppliers({pagination: this.pagination});
  };

  async fetchSuppliers({pagination}: { pagination: IPagination }) {
      this.loading = true;

      pagination.limit = pagination.rowsPerPage;
      pagination.rowsPerPage = null;

      await this.updateSupplierRequestParams({
          ...pagination,
          filter: this.search,
      });
      this.pagination.rowsNumber = this.suppliersData.total;
      this.pagination.rowsPerPage = this.suppliersData.limit;
      this.pagination.page = this.suppliersData.page;
      this.pagination.sortBy = pagination.sortBy;
      this.pagination.descending = pagination.descending;

      this.loading = false;
  };

  showSupplierEditModal(supplier: ISupplier) {
      this.$q.dialog({
          component: SupplierEdit,
          parent: this,
          supplierId: supplier.id,
      });
  };

  showSupplierCreateModal() {
      this.$q.dialog({
          component: SupplierCreate,
          parent: this,
      });
  };
};
</script>
