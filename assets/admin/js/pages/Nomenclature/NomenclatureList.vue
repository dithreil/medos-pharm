<template>
  <div class="page-bottom-margin">
    <div class="row no-wrap justify-between items-center">
      <h1 class="text-h4 text-primary">
        Номенклатура
      </h1>
      <q-btn
          flat
          color="primary"
          label="Создать номенклатуру"
          :to="{name: 'NomenclatureCreate'}"
      />
    </div>
    <q-table
        :data="tableData"
        :columns="tableColumns"
        :pagination.sync="pagination"
        :loading="loading"
        :filter="search"
        no-results-label="Список номенклатуры пуст"
        binary-state-sort
        @request="fetchNomenclatures"
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
                title="Редактировать номенклатуру"
                :to="{name: 'NomenclatureEdit', params: {nomenclatureId: props.row.id}}"
            />
            <q-btn
                dense
                flat
                color="grey"
                title="Подробнее"
                icon="info"
                :to="{name: 'NomenclatureDetails', params: {nomenclatureId: props.row.id}}"
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
import {INomenclature, INomenclatureData} from '../../interfaces/nomenclature';


@Component({
    computed: {
        ...mapGetters({
            nomenclaturesData: 'nomenclature/nomenclaturesData',
            nomenclatureRequestParams: 'nomenclature/nomenclatureRequestParams',
        }),
    },
    methods: {
        ...mapActions({
            updateNomenclatureRequestParams: 'nomenclature/updateNomenclatureRequestParams',
        }),
    },
})
export default class NomenclatureList extends Vue {
  protected nomenclaturesData!: INomenclatureData;
  protected updateNomenclatureRequestParams!: (payload: IRequestParams) => any;

  private tableColumns = [
      {name: 'name', align: 'left', label: 'Наименование', field: 'name', sortable: true},
      {name: 'producer', align: 'left', label: 'Производитель', field: (row: INomenclature) => row.producer.shortName},
      {name: 'medicalForm', align: 'left', label: 'Медицинская форма', field: 'medicalForm'},
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
      return this.nomenclaturesData?.items || [];
  };

  mounted() {
      this.fetchNomenclatures({pagination: this.pagination});
  };

  async fetchNomenclatures({pagination}: { pagination: IPagination }) {
      this.loading = true;

      pagination.limit = pagination.rowsPerPage;
      pagination.rowsPerPage = null;

      await this.updateNomenclatureRequestParams({
          ...pagination,
          filter: this.search,
      });
      this.pagination.rowsNumber = this.nomenclaturesData.total;
      this.pagination.rowsPerPage = this.nomenclaturesData.limit;
      this.pagination.page = this.nomenclaturesData.page;
      this.pagination.sortBy = pagination.sortBy;
      this.pagination.descending = pagination.descending;

      this.loading = false;
  };
};
</script>
