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
                        {{$loc('producerEdit')}}
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

<script>
import {mapActions} from 'vuex';
import * as validationHelpers from '../../validation/helpers';


export default {
    name: 'ProducerEdit',
    props: {
        producer: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            model: null,
        };
    },
    async created() {
        this.model = JSON.parse(JSON.stringify(this.producer));
    },
    methods: {
        ...mapActions({
            editProducerData: 'producer/editProducerData',
        }),
        ...validationHelpers,
        isFormInvalid() {
            return this.$refs.form.validate();
        },
        show() {
            this.$refs.dialog.show();
        },
        hide() {
            this.$refs.dialog.hide();
        },
        onDialogHide() {
            this.$emit('hide');
        },
        onCancelClick() {
            this.hide();
        },
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
        },
    },
};
</script>
