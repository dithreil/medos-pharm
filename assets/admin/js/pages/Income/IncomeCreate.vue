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
          label="Создание поступления товаров"
      />
    </q-breadcrumbs>
    <q-card class="emploee-edit">
      <q-form
          class="emploee-edit__form col w-100"
          ref="form"
          @submit="save"
      >
        <q-card-section>
          <div class="text-h6">
            Создать поступление товаров
          </div>
        </q-card-section>
        <q-card-section>
          <div class="row">
            <div class="col-4" style="padding: 0 30px">
              <q-input filled v-model="model.date">
                <template v-slot:append>
                  <q-icon name="event" class="cursor-pointer">
                    <q-popup-proxy transition-show="scale" transition-hide="scale">
                      <q-date v-model="model.date" mask="DD.MM.YYYY HH:mm:SS">
                        <div class="row items-center justify-end">
                          <q-btn v-close-popup label="Close" color="primary" flat />
                        </div>
                      </q-date>
                    </q-popup-proxy>
                  </q-icon>
                  <q-icon name="access_time" class="cursor-pointer">
                    <q-popup-proxy transition-show="scale" transition-hide="scale">
                      <q-time v-model="model.date" mask="DD.MM.YYYY HH:mm:SS" format24h>
                        <div class="row items-center justify-end">
                          <q-btn v-close-popup label="Close" color="primary" flat />
                        </div>
                      </q-time>
                    </q-popup-proxy>
                  </q-icon>
                </template>
              </q-input>
            </div>
            <div class="col-4" style="padding: 0 30px">
              <q-select
                  v-model="model.store"
                  option-label="name"
                  emit-value
                  :options="storesData.items || [] "
                  label="Торговая точка"
                  use-input
                  filled
                  @filter="fetchStores"
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
                      <q-item-label caption>{{ scope.opt.address }}</q-item-label>
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
            <div class="col-4" style="padding: 0 30px">
              <q-select
                  v-model="model.supplier"
                  option-label="name"
                  emit-value
                  :options="suppliersData.items || [] "
                  label="Поставщик"
                  use-input
                  filled
                  @filter="fetchSuppliers"
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
                      <q-item-label caption>{{ scope.opt.address }}</q-item-label>
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
          </div>
          <DocumentTable :table-rows="model.rows" />
        </q-card-section>
        <q-card-actions align="right">
          <q-btn
              unelevated
              label="Сохранить"
              color="primary"
              type="submit"
          />
        </q-card-actions>
      </q-form>
    </q-card>
  </div>
</template>

<script lang="ts">
import {mapActions, mapGetters} from 'vuex';
import * as validationHelpers from '../../validation/helpers';
import {Component, Ref, Vue} from 'vue-property-decorator';
import {IDocumentIncomeCreateEditData, IDocumentIncomeDetails} from '../../interfaces/income';
import {QForm} from 'quasar';
import {incomeCreate} from '../../models/CreateModels';
import {IRequestParams} from '../../interfaces/request-params';
import {IStoreData} from '../../interfaces/store';
import {ISupplierData} from '../../interfaces/supplier';
import DocumentTable from '../../components/Document/DocumentTable.vue';
import {prepareDocumentRows} from '../../utils/documentAdapter';
import moment from 'moment';


@Component({
    components: {DocumentTable},
    computed: {
        ...mapGetters({
            suppliersData: 'supplier/suppliersData',
            storesData: 'store/storesData',
            user: 'user/userData',
        }),
    },
    methods: {
        ...mapActions({
            createIncome: 'income/createIncome',
            updateStoreRequestParams: 'store/updateStoreRequestParams',
            updateSupplierRequestParams: 'supplier/updateSupplierRequestParams',
        }),
        ...validationHelpers,
    },
})
export default class IncomeCreate extends Vue {
  @Ref('form') readonly form!: QForm;

  protected updateStoreRequestParams!: (payload: IRequestParams) => any;
  protected updateSupplierRequestParams!: (payload: IRequestParams) => any;
  protected createIncome!: ({payload}: {payload: IDocumentIncomeCreateEditData}) => any;

  protected storesData!: IStoreData
  protected suppliersData!: ISupplierData

  protected model: IDocumentIncomeDetails = JSON.parse(JSON.stringify(incomeCreate));

  mounted() {
      this.model.date = moment().format('DD.MM.YYYY HH:mm:SS');
  }

  isFormInvalid() {
      return this.form.validate();
  };

  fetchStores(val : string, update : () => any) {
      this.updateStoreRequestParams({...this.$constants.systemConstants.selectPagination, filter: val})
          .finally(() => {
              update();
          });
  }

  fetchSuppliers(val : string, update : () => any, abort: () => any) {
      if (2 > val.length) {
          abort();

          return;
      }
      this.updateSupplierRequestParams({...this.$constants.systemConstants.selectPagination, filter: val})
          .finally(() => {
              update();
          });
  }
  async save() {
      const isValid = await this.isFormInvalid();

      if (!isValid) return;

      const dataToServer: IDocumentIncomeCreateEditData = {
          comment: '',
          date: this.model.date,
          storeId: this.model.store.id,
          supplierId: this.model.supplier.id,
          rows: prepareDocumentRows(this.model.rows),
      };

      const response = await this.createIncome({payload: dataToServer});
      if (201 === response.status) {
          await this.$router.push({name: 'IncomeList'});
      }
  };
};
</script>
