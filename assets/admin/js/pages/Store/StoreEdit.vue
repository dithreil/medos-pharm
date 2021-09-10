<template>
    <q-dialog ref="dialog" @hide="onDialogHide">
        <q-card style="min-width: 320px">
            <q-form
                ref="form"
                class="col"
                style="max-width: 500px;"
            >
                <q-card-section>
                    <div class="text-h6">
                       Редактировать поставщика
                    </div>
                </q-card-section>
                <q-card-section>
                    <div class="store__input">
                      <q-input
                          v-model="model.name"
                          outlined
                          label="Наименование"
                          lazy-rules
                          :rules="[
                                (val) => !!val || $errorMessages.REQUIRED,
                            ]"
                      />
                      <q-input
                          v-model="model.address"
                          outlined
                          label="Адрес"
                          lazy-rules
                          :rules="[
                                (val) => !!val || $errorMessages.REQUIRED,
                            ]"
                      />
                      <q-input
                          v-model="model.description"
                          outlined
                          type="textarea"
                          label="Доп. информация"
                          lazy-rules
                          :rules="[
                            (val) => !!val || $errorMessages.REQUIRED,
                        ]"
                      />
                    </div>
                </q-card-section>
                <q-card-actions align="right">
                    <q-btn
                        unelevated
                        label="Закрыть"
                        color="grey-5"
                        @click="onCancelClick"
                    />
                    <q-btn
                        unelevated
                        label="Сохранить"
                        color="primary"
                        @click="onOkClick"
                    />
                </q-card-actions>
            </q-form>
        </q-card>
    </q-dialog>
</template>

<script lang="ts">
import {mapActions} from 'vuex';
import * as validationHelpers from '../../validation/helpers';
import {Component, Prop, Ref, Vue} from 'vue-property-decorator';
import {IStoreDetails} from '../../interfaces/store';
import {QDialog, QForm} from 'quasar';
import {storeCreate} from '../../models/CreateModels';
import {IServerResponse} from '../../interfaces/request-params';

@Component({
    methods: {
        ...mapActions({
            getStoreDetails: 'store/getStoreDetails',
            editStoreData: 'store/editStoreData',
        }),
        ...validationHelpers,
    },
})
export default class StoreEdit extends Vue {
  protected editStoreData!: ({id, payload}: {id: string, payload: IStoreDetails}) => any;
  protected getStoreDetails!: (id: string) => IServerResponse;


  @Prop({type: String, required: true}) readonly storeId!: string;

  @Ref('dialog') readonly dialog!: QDialog;
  @Ref('form') readonly form!: QForm;

  protected model: IStoreDetails = JSON.parse(JSON.stringify(storeCreate));

  async mounted() {
      const response = await this.getStoreDetails(this.storeId);
      this.model = response.data;
  };

  isFormInvalid() {
      return this.form.validate();
  };
  show() {
      this.dialog.show();
  };
  hide() {
      this.dialog.hide();
  };
  onDialogHide() {
      this.$emit('hide');
  };
  onCancelClick() {
      this.hide();
  };
  async onOkClick() {
      const isValid = await this.isFormInvalid();
      if (!isValid) return;

      try {
          await this.editStoreData({id: this.model.id, payload: this.model});
          this.$emit('ok');
          this.hide();
      } catch (error) {
          console.log(error);
      }
  };
};
</script>
