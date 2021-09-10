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
                    <div class="producer__input">
                        <q-input
                            v-model="model.fullName"
                            outlined
                            label="Полное наименование"
                            lazy-rules
                            :rules="[
                                (val) => !!val || $errorMessages.REQUIRED,
                            ]"
                        />
                        <q-input
                            v-model="model.shortName"
                            outlined
                            label="Сокращенно"
                            lazy-rules
                            :rules="[
                                (val) => !!val || $errorMessages.REQUIRED,
                            ]"
                        />
                        <q-input
                            v-model="model.country"
                            outlined
                            label="Страна"
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
import {IProducerDetails} from '../../interfaces/producer';
import {IProducer} from '../../interfaces/producer';
import {QDialog, QForm} from 'quasar';
import {producerCreate} from '../../models/CreateModels';

@Component({
    methods: {
        ...mapActions({
            createProducer: 'producer/createProducer',
        }),
        ...validationHelpers,
    },
})
export default class ProducerCreate extends Vue {
  protected createProducer!: ({payload}: {payload: IProducer}) => any;

  @Ref('dialog') readonly dialog!: QDialog;
  @Ref('form') readonly form!: QForm;

  protected model: IProducerDetails = JSON.parse(JSON.stringify(producerCreate));
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
          await this.createProducer({payload: {...this.model}});
          this.$emit('ok');
          this.hide();
      } catch (error) {
          console.log(error);
      }
  };
}
</script>
