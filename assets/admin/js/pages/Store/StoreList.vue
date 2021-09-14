<template>
  <div class="page-bottom-margin">
    <div class="row no-wrap justify-between items-center">
      <h1 class="text-h4 text-primary">
        Торговые точки
      </h1>
      <q-btn
          flat
          color="primary"
          label="Создать торговую точку"
          @click="showStoreCreateModal"
      />
    </div>
    <q-table
        :data="tableData"
        :columns="tableColumns"
        :pagination.sync="pagination"
        :loading="loading"
        :filter="search"
        no-results-label="Список торговых точек пуст"
        binary-state-sort
        @request="fetchStores"
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
                title="Редактировать торговую точку"
                @click="showStoreEditModal(props.row)"
            />
          </div>
        </q-td>
      </template>

    </q-table>
  </div>
</template>

<script lang="ts">
import {mapActions, mapGetters} from 'vuex';
import StoreEdit from './StoreEdit.vue';
import StoreCreate from './StoreCreate.vue';
import {Component, Vue} from 'vue-property-decorator';
import {IPagination, IRequestParams} from '../../interfaces/request-params';
import {IStore, IStoreData} from '../../interfaces/store';


@Component({
    computed: {
        ...mapGetters({
            storesData: 'store/storesData',
            storeRequestParams: 'store/storeRequestParams',
        }),
    },
    methods: {
        ...mapActions({
            updateStoreRequestParams: 'store/updateStoreRequestParams',
        }),
    },
})
export default class StoreList extends Vue {
  protected storesData!: IStoreData;
  protected updateStoreRequestParams!: (payload: IRequestParams) => any;

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
      return this.storesData?.items || [];
  };

  mounted() {
      this.fetchStores({pagination: this.pagination});
  };

  async fetchStores({pagination}: { pagination: IPagination }) {
      this.loading = true;

      pagination.limit = pagination.rowsPerPage;
      pagination.rowsPerPage = null;

      await this.updateStoreRequestParams({
          ...pagination,
          filter: this.search,
      });
      this.pagination.rowsNumber = this.storesData.total;
      this.pagination.rowsPerPage = this.storesData.limit;
      this.pagination.page = this.storesData.page;
      this.pagination.sortBy = pagination.sortBy;
      this.pagination.descending = pagination.descending;

      this.loading = false;
  };

  showStoreEditModal(store: IStore) {
      this.$q.dialog({
          component: StoreEdit,
          parent: this,
          storeId: store.id,
      });
  };

  showStoreCreateModal() {
      this.$q.dialog({
          component: StoreCreate,
          parent: this,
      });
  };
};
</script>
