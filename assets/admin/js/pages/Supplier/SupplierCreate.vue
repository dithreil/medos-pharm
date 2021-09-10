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
                        Создать производителя
                    </div>
                </q-card-section>
                <q-card-section>
                    <div class="Supplier__input">
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
                          v-model="model.phoneNumber"
                          outlined
                          mask="+7 (###) ###-##-##"
                          fill-mask
                          unmasked-value
                          label="Телефон"
                          lazy-rules
                          :rules="[
                            (val) => phoneRule(val) || $errorMessages.INVALID_PHONE,
                            (val) => !!val || $errorMessages.REQUIRED,
                        ]"
                      />
                      <q-input
                          v-model="model.email"
                          outlined
                          label="Email"
                          lazy-rules
                          :rules="[
                            (val) => emailRule(val) || $errorMessages.INVALID_EMAIL,
                            (val) => !!val || $errorMessages.REQUIRED,
                        ]"
                      />
                      <q-input
                          v-model="model.information"
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
import {Component, Ref, Vue} from 'vue-property-decorator';
import {ISupplierDetails} from '../../interfaces/Supplier';
import {ISupplier} from '../../interfaces/Supplier';
import {QDialog, QForm} from 'quasar';
import {supplierCreate} from '../../models/CreateModels';

@Component({
    methods: {
        ...mapActions({
            createSupplier: 'supplier/createSupplier',
        }),
        ...validationHelpers,
    },
})
export default class SupplierEdit extends Vue {
  protected createSupplier!: ({payload}: {payload: ISupplier}) => any;

  @Ref('dialog') readonly dialog!: QDialog;
  @Ref('form') readonly form!: QForm;

  protected model: ISupplierDetails = JSON.parse(JSON.stringify(supplierCreate));
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
          await this.createSupplier({payload: {...this.model}});
          this.$emit('ok');
          this.hide();
      } catch (error) {
          console.log(error);
      }
  };
}
</script>
