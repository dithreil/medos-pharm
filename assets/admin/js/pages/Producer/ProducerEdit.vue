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
                       Редактировать производителя
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
import {Component, Prop, Ref, Vue} from 'vue-property-decorator';
import {IProducerDetails} from '../../interfaces/producer';
import {QDialog, QForm} from 'quasar';
import {producerCreate} from '../../models/CreateModels';
import {IServerResponse} from '../../interfaces/request-params';

@Component({
    methods: {
        ...mapActions({
            getProducerDetails: 'producer/getProducerDetails',
            editProducerData: 'producer/editProducerData',
        }),
        ...validationHelpers,
    },
})
export default class ProducerEdit extends Vue {
  protected editProducerData!: ({id, payload}: {id: string, payload: IProducerDetails}) => any;
  protected getProducerDetails!: (id: string) => IServerResponse;


  @Prop({type: String, required: true}) readonly producerId!: string;

  @Ref('dialog') readonly dialog!: QDialog;
  @Ref('form') readonly form!: QForm;

  protected model: IProducerDetails = JSON.parse(JSON.stringify(producerCreate));

  async mounted() {
      const response = await this.getProducerDetails(this.producerId);

      console.log(response);
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
          await this.editProducerData({id: this.model.id, payload: this.model});
          this.$emit('ok');
          this.hide();
      } catch (error) {
          console.log(error);
      }
  };
};
</script>
