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
          @submit="saveOnly"
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
                  :options="storeTableData"
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
                  :options="supplierTableData"
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
          <DocumentTable :model="model" />
        </q-card-section>
        <q-card-actions align="left">
          <q-btn
              unelevated
              label="Сохранить"
              color="primary"
              type="submit"
          />
          <q-btn
              unelevated
              label="Провести"
              color="primary"
              type="submit"
              @click="saveAndPost"
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
import {IDocumentIncomeCreateEditData} from '../../interfaces/income';
import {QForm} from 'quasar';
import {IRequestParams} from '../../interfaces/request-params';
import {IStoreData} from '../../interfaces/store';
import {ISupplierData} from '../../interfaces/supplier';
import DocumentTable from '../../components/Document/DocumentTable.vue';
import moment from 'moment';
import IncomeDocument from '../../models/IncomeDocument';
import {error} from '../../utils/notifizer';


@Component({
    components: {DocumentTable},
    computed: {
        ...mapGetters({
            suppliersData: 'supplier/suppliersData',
            storesData: 'store/storesData',
            // user: 'user/userData',
        }),
    },
    methods: {
        ...mapActions({
            postIncome: 'income/postIncome',
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
  protected postIncome!: ({payload}: {payload: {action: string}, id: string}) => any;


  protected storesData!: IStoreData
  protected suppliersData!: ISupplierData

  protected model: IncomeDocument = new IncomeDocument();

  get storeTableData() {
      return this.storesData?.items || [];
  };

  get supplierTableData() {
      return this.suppliersData?.items || [];
  };


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

  fetchSuppliers(val : string, update : () => any) {
      this.updateSupplierRequestParams({...this.$constants.systemConstants.selectPagination, filter: val})
          .finally(() => {
              update();
          });
  }
  async saveDocument() {
      const isValid = await this.model.isValid();

      if (!isValid) {
          error('Данные формы некорректны!');

          return null;
      }

      return await this.createIncome({payload: this.model.getDataForServer()});
  };

  async saveOnly() {
      const response = await this.saveDocument();

      if (response && 201 === response.status) {
          await this.$router.push({name: 'IncomeEdit', params: {id: response.data.id}});
      }
  }

  async saveAndPost() {
      const response = await this.saveDocument();

      if (response && 201 === response.status) {
          const postResponse = await this.postIncome({id: response.data.id, payload: {action: 'income.enter'}});
          if (204 === postResponse.status) {
              await this.$router.push({name: 'IncomeEdit', params: {id: response.data.id}});
          }
      }
  }
};
</script>
