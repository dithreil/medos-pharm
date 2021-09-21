<template>
  <div>
    <q-breadcrumbs class="q-mb-xl employee-details__breadcrumbs">
      <q-breadcrumbs-el icon="home">
        <a class="q-breadcrumbs__el q-link" href="/admin">Панель Администратора</a>
      </q-breadcrumbs-el>
      <q-breadcrumbs-el
          icon="widgets"
          label="Поступления товаров"
          :to="{name: 'IncomeList'}"
      />
      <q-breadcrumbs-el
          icon="face"
          :label="'Поступление от ' + model.date"
      />
    </q-breadcrumbs>
    <q-card class="emploee-edit">
        <q-card-section>
          <div class="row">
            <div class="col-4" style="padding: 0 30px">
              <q-input label="Дата поступления" filled v-model="model.date" readonly />
            </div>
            <div class="col-4" style="padding: 0 30px">
              <q-input label="Торговая точка" filled v-model="model.store.name" readonly />
            </div>
            <div class="col-4" style="padding: 0 30px">
              <q-input label="Поставщик" filled v-model="model.supplier.name" readonly />
            </div>
          </div>
          <q-table
              style="margin: 30px"
              :data="model.rows"
              :columns="rowsTableColumns"
              no-results-label="Список товаров пуст"
              binary-state-sort
          >
            <template #body-cell-index="props">
              <q-td key="index" :props="props">
                <div class="row items-start q-gutter-xs cursor-pointer">
                  {{ model.rows.indexOf(props.row) + 1 }}
                </div>
              </q-td>
            </template>
          </q-table>
        </q-card-section>
    </q-card>
  </div>
</template>

<script lang="ts">
import {mapActions} from 'vuex';
import {Component, Prop, Vue} from 'vue-property-decorator';
import {IDocumentIncomeDetails, ITableRowIncome} from '../../interfaces/income';
import {incomeCreate} from '../../models/CreateModels';
import {IServerResponse} from '../../interfaces/request-params';
@Component({
    methods: {
        ...mapActions({
            getIncomeDetails: 'income/getIncomeDetails',
        }),
    },
})
export default class IncomeDetails extends Vue {
  @Prop({type: String, required: true}) readonly incomeId!: string;
  protected getIncomeDetails!: (id: string) => IServerResponse;

  protected model: IDocumentIncomeDetails = JSON.parse(JSON.stringify(incomeCreate));

  private rowsTableColumns = [
      {name: 'index', align: 'left', label: 'Id', sortable: true},
      {field: (row: ITableRowIncome) => row.nomenclature?.name || '',
          name: 'nomenclature', align: 'left', label: 'Номенклатура'},
      {field: (row: ITableRowIncome) => row.nomenclature?.producer.shortName || '',
          name: 'producer', align: 'left', label: 'Производитель'},
      {field: (row: ITableRowIncome) => row.characteristic.serial, align: 'left', label: 'Серия'},
      {field: (row: ITableRowIncome) => row.characteristic.expire, align: 'left', label: 'Срок годности'},
      {field: 'value', name: 'value', align: 'left', label: 'Количество'},
      {field: 'purchasePrice', name: 'purchasePrice', align: 'left', label: 'Цена покупки'},
      {field: 'retailPrice', name: 'retailPrice', align: 'left', label: 'Цена продажи'},
  ];

  async mounted() {
      const response = await this.getIncomeDetails(this.incomeId);
      this.model = response.data;
  };
};
</script>
