<template>
  <div>
    <q-breadcrumbs class="q-mb-xl employee-details__breadcrumbs">
      <q-breadcrumbs-el icon="home">
        <a class="q-breadcrumbs__el q-link" href="/admin">Панель Администратора</a>
      </q-breadcrumbs-el>
      <q-breadcrumbs-el
          icon="widgets"
          label="Номенклатура"
          :to="{name: 'NomenclatureList'}"
      />
      <q-breadcrumbs-el
          icon="face"
          label="Создание номенклатуры"
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
            Создать номенклатуру
          </div>
        </q-card-section>
        <q-card-section class="q-p-none">
          <q-input
              v-model="model.name"
              outlined
              label="Наименование"
              lazy-rules
              :rules="[
                            (val) => !!val || $errorMessages.REQUIRED,
                        ]"
          />
          <div>
            <q-select
                v-model="model.producer"
                option-label="shortName"
                emit-value
                :options="producersData.items || [] "
                label="Производитель"
                use-input

                outlined
                @filter="fetchProducers"
                lazy-rules
                :rules="[
                                    (val) => !!val || $errorMessages.REQUIRED,
                                ]"
                behavior="menu"
            >
              <template v-slot:no-option>
                <q-item>
                  <q-item-section class="text-grey">
                    Нет результата
                  </q-item-section>
                </q-item>
              </template>
            </q-select>
          </div>
          <div>
            <q-select
                v-model="model.medicalForm"
                emit-value
                map-options
                :options="medFormsFiltered"
                label="Медицинская форма"
                use-input
                option-label="name"
                outlined
                @filter="fetchMedForms"
                lazy-rules
                :rules="[
                                    (val) => !!val || $errorMessages.REQUIRED,
                                ]"
                behavior="menu"
            >
              <template v-slot:no-option>
                <q-item>
                  <q-item-section class="text-grey">
                    Нет результата
                  </q-item-section>
                </q-item>
              </template>
            </q-select>
          </div>
          <q-checkbox
            v-model="model.isVat"
            outlined
            label="НДС"
          />
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
import {INomenclatureCreateEditData, INomenclatureDetails} from '../../interfaces/nomenclature';
import {QForm} from 'quasar';
import {nomenclatureCreate} from '../../models/CreateModels';
import {IRequestParams} from '../../interfaces/request-params';
import {IProducerData} from '../../interfaces/producer';

@Component({
    computed: {
        ...mapGetters({
            producersData: 'producer/producersData',
            medFormsData: 'nomenclature/medFormsData',
        }),
    },
    methods: {
        ...mapActions({
            createNomenclature: 'nomenclature/createNomenclature',
            getMedFormsList: 'nomenclature/getMedFormsList',
            updateProducerRequestParams: 'producer/updateProducerRequestParams',
        }),
        ...validationHelpers,
    },
})
export default class NomenclatureCreate extends Vue {
  protected createNomenclature!: ({payload}: {payload: INomenclatureCreateEditData}) => any

  protected updateProducerRequestParams!: (payload: IRequestParams) => any;
  protected getMedFormsList!: () => any;
  protected producersData!: IProducerData;

  protected medFormsData!: Array<string>;

  @Ref('form') readonly form!: QForm;

  protected model: INomenclatureDetails = JSON.parse(JSON.stringify(nomenclatureCreate));

  protected medFormsFiltered: Array<string> = [];

  mounted() {
      this.getMedFormsList();
  };

  isFormInvalid() {
      return this.form.validate();
  };

  fetchProducers(val : string, update : () => any, abort: () => any) {
      if (3 > val.length) {
          abort();

          return;
      }

      this.updateProducerRequestParams({...this.$constants.systemConstants.selectPagination, filter: val})
          .finally(() => {
              update();
          });
  }

  fetchMedForms(val : string, update : () => any, abort: () => any) {
      if (this.medFormsData) {
          const str: string = val.toLowerCase();
          this.medFormsFiltered = this.medFormsData.filter((p) => p.toLowerCase().includes(str));
          update();
      } else {
          abort();
      }
  }

  async save() {
      const isValid = await this.isFormInvalid();

      if (!isValid) return;

      const dataToServer: INomenclatureCreateEditData = {
          producer: this.model.producer.id,
          name: this.model.name,
          medicalForm: this.model.medicalForm,
          isVat: true,
      };

      const response = await this.createNomenclature({payload: dataToServer});
      if (201 === response.status) {
          await this.$router.push({name: 'NomenclatureList'});
      }
  };
};
</script>
