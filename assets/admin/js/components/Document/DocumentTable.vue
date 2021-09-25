<template>
        <q-table
            title="Товары"
            style="margin: 30px"
            :data="model.rows"
            :columns="rowsTableColumns"
            no-results-label="Список товаров пуст"
            :row-key="(row) => model.rows.indexOf(row)"
        >
          <template v-slot:body="props">
            <q-tr :props="props">
              <q-td key="index" :props="props">
                <div class="row items-start q-gutter-xs cursor-pointer">
                  {{ props.key + 1 }}
                </div>
              </q-td>
              <q-td
                  :class="{'err-cell-underline': !props.row.nomenclature}"
                  class="cursor-pointer"
                  key="nomenclature"
                  :props="props"
              >
                <div class="row items-start q-gutter-xs">

                  {{ props.row.nomenclature ? props.row.nomenclature.name : '' }}
                </div>
                <q-popup-edit auto-save v-model="props.row.nomenclature">
                  <div class="row">
                    <div class="col-11">
                      <q-select
                      v-model="props.row.nomenclature"
                      option-label="name"
                      emit-value
                      :options="nomenclaturesData.items || [] "
                      label="Выбрать номенклатуру"
                      use-input
                      filled
                      @filter="fetchNomenclature"
                      lazy-rules
                      :rules="[
                                    (val) => !!val || $errorMessages.REQUIRED,
                                ]"
                      behavior="menu"
                  >
                    <template v-slot:option="scope">
                      <q-item  v-bind="scope.itemProps"
                               v-on="scope.itemEvents"
                      >
                        <q-item-section>
                          <q-item-label v-html="scope.opt.name" />
                          <q-item-label caption>{{ scope.opt.producer.shortName }}</q-item-label>
                        </q-item-section>
                      </q-item>
                    </template>
                    <template v-slot:no-option>
                      <q-item>
                        <q-item-section class="text-grey">
                          Нет результата
                        </q-item-section>
                      </q-item>
                    </template>
                  </q-select>
                    </div>
                    <div class="col-1">
                      <q-btn
                          style="margin-top: 5px"
                          flat
                          @click="showNomenclatureCreateModal"
                          round
                          color="primary"
                          icon="add"
                      />
                    </div>
                  </div>
                </q-popup-edit>
              </q-td>
              <q-td key="producer" :props="props">
                <div class="row items-start q-gutter-xs cursor-pointer">
                  {{props.row.nomenclature ? props.row.nomenclature.producer.shortName : ''}}
                </div>
              </q-td>
              <q-td
                  class="cursor-pointer"
                  key="serial"
                  :props="props"
                  :class="{'err-cell-underline': !props.row.characteristic.serial}"
              >
                <div class="row items-start q-gutter-xs cursor-pointer">
                  {{props.row.characteristic.serial}}
                </div>
                <q-popup-edit auto-save v-model="props.row.characteristic.serial">
                  <q-input
                      v-model="props.row.characteristic.serial"
                      dense
                      autofocus
                      :rules="[
                                    (val) => !!val || $errorMessages.REQUIRED,
                                ]"
                  />
                </q-popup-edit>
              </q-td>
              <q-td
                  class="cursor-pointer"
                  key="expire"
                  :class="{'err-cell-underline': !props.row.characteristic.expireOriginal}"
                  :props="props"
              >
                <div class="row items-start q-gutter-xs cursor-pointer">
                  {{props.row.characteristic.expire}}
                </div>
                <q-popup-edit auto-save v-model="props.row.characteristic.expireOriginal">
                  <q-input
                      autofocus
                      dense
                      v-model="props.row.characteristic.expireOriginal"
                      @input="expireDateChanged(props.row.characteristic)"
                      :rules="[
                                    (val) => !!val || $errorMessages.REQUIRED,
                                ]"
                  >
                    <template v-slot:append>
                      <q-icon name="event" class="cursor-pointer">
                        <q-popup-proxy ref="qDateProxy" transition-show="scale" transition-hide="scale">
                          <q-date
                              @input="expireDateChanged(props.row.characteristic)"
                              mask="DD.MM.YYYY"
                              v-model="props.row.characteristic.expireOriginal"
                          >
                            <div class="row items-center justify-end">
                              <q-btn v-close-popup label="Close" color="primary" flat />
                            </div>
                          </q-date>
                        </q-popup-proxy>
                      </q-icon>
                    </template>
                  </q-input>
                </q-popup-edit>
              </q-td>

              <q-td
                  key="value"
                  :props="props"
                  :class="{'err-cell-underline': !props.row.value}"
              >
                <div class="row items-start q-gutter-xs cursor-pointer">
                  {{props.row.value || 0 }} шт.
                  <q-popup-edit auto-save v-model="props.row.value">
                    <q-input
                        type="number"
                        v-model.number="props.row.value"
                        dense
                        autofocus
                        :rules="[
                                    (val) => !!val || $errorMessages.REQUIRED,
                                ]"
                    />
                  </q-popup-edit>
                </div>
              </q-td>
              <q-td
                  key="purchasePrice"
                  :props="props"
                  :class="{'err-cell-underline': !props.row.purchasePrice}"
              >
                <div class="row items-start q-gutter-xs cursor-pointer">
                  {{props.row.purchasePrice || 0 }} руб.
                  <q-popup-edit auto-save v-model="props.row.purchasePrice">
                    <q-input
                        type="number"
                        v-model.number="props.row.purchasePrice"
                        dense
                        autofocus
                        :rules="[
                                    (val) => !!val || $errorMessages.REQUIRED,
                                ]"
                    />
                  </q-popup-edit>
                </div>
              </q-td>
              <q-td
                  key="retailPrice"
                  :props="props"
                  :class="{'err-cell-underline': !props.row.retailPrice}"
              >
                <div class="row items-start q-gutter-xs cursor-pointer">
                  {{props.row.retailPrice || 0}} руб.
                  <q-popup-edit auto-save v-model="props.row.retailPrice">
                    <q-input
                        type="number"
                        v-model.number="props.row.retailPrice"
                        dense
                        autofocus
                        :rules="[
                                    (val) => !!val || $errorMessages.REQUIRED,
                                ]"
                    />
                  </q-popup-edit>
                </div>
              </q-td>
            </q-tr>
          </template>
          <template #top-left>
            <q-btn
                round
                color="primary"
                size="12px"
                icon="add_circle"
                @click="addRowToTable"
            />
          </template>
        </q-table>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import {IRequestParams} from '../../interfaces/request-params';
import {ICharacteristic, INomenclatureData} from '../../interfaces/nomenclature';
import {mapActions, mapGetters} from 'vuex';
import * as validationHelpers from '../../validation/helpers';
import NomenclatureCreateModal from '../../pages/Nomenclature/NomenclatureCreateModal.vue';
import IncomeDocument from '../../models/IncomeDocument';
@Component({
    computed: {
        ...mapGetters({
            nomenclaturesData: 'nomenclature/nomenclaturesData',
        }),
    },
    methods: {
        ...mapActions({
            updateNomenclatureRequestParams: 'nomenclature/updateNomenclatureRequestParams',
        }),
        ...validationHelpers,
    },
})
export default class DocumentTable extends Vue {
  @Prop({type: Object, default: () => []}) readonly model!: IncomeDocument;

  // protected selectedRows: Array<ITableRowIncome> = [];
  protected updateNomenclatureRequestParams!: (payload: IRequestParams) => any;
  protected nomenclaturesData!: INomenclatureData;

  private rowsTableColumns = [
      {name: 'index', align: 'left', label: 'Id', sortable: true},
      {name: 'nomenclature', align: 'left', label: 'Номенклатура'},
      {name: 'producer', align: 'left', label: 'Производитель'},
      {name: 'serial', align: 'left', label: 'Серия'},
      {name: 'expire', align: 'left', label: 'Срок годности'},
      {name: 'value', align: 'left', label: 'Количество'},
      {name: 'purchasePrice', align: 'left', label: 'Цена покупки'},
      {name: 'retailPrice', align: 'left', label: 'Цена продажи'},
  ];


  fetchNomenclature(val : string, update : () => any, abort: () => any) {
      if (2 > val.length) {
          abort();

          return;
      }
      this.updateNomenclatureRequestParams({...this.$constants.systemConstants.selectPagination, filter: val})
          .finally(() => {
              update();
          });
  }
  addRowToTable() {
      this.model.addNewRow();
  }

  expireDateChanged(characteristic: ICharacteristic) {
      if (!characteristic.expireOriginal) {
          characteristic.expire = '';

          return;
      }
      const newExpireArr: Array<string> = characteristic.expireOriginal.split('.');
      newExpireArr.shift();
      characteristic.expire = newExpireArr.join('/');
  }

  showNomenclatureCreateModal() {
      this.$q.dialog({
          component: NomenclatureCreateModal,
          parent: this,
      });
  };
};
</script>
<style>
.err-cell-underline {
  border-bottom: red 2px dotted !important;
  line-height:2em;
}

</style>
