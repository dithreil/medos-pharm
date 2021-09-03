<template>
    <q-card
        flat
        bordered
        class="my-card bg-grey-1"
    >
        <div  class="card">
            <div class="card-header">
                <h3 class="text-h4">
                    Изменить пароль
                </h3>
            </div>
            <q-form
                ref="form"
                class="q-gutter-md col card-form"
                @submit="save"
            >
                <q-input
                    v-model="model.oldPassword"
                    filled :type="toggle_OldPassword ? 'password' : 'text'"
                    lazy-rules
                    :rules="[
                        (val) => !!val || $errorMessages.REQUIRED,
                    ]"
                    label="Старый пароль"
                >
                    <template v-slot:append>
                        <q-icon
                            :name="toggle_OldPassword ? 'visibility_off' : 'visibility'"
                            class="cursor-pointer"
                            @click="toggle_OldPassword = !toggle_OldPassword"
                        />
                    </template>
                </q-input>
                <q-input
                    v-model="model.newPassword"
                    filled
                    :type="toggle_NewPassword ? 'password' : 'text'"
                    lazy-rules
                    :rules="[
                        (val) => !!val || $errorMessages.REQUIRED,
                    ]"
                    label="Новый пароль"
                >
                    <template v-slot:append>
                        <q-icon
                            :name="toggle_NewPassword ? 'visibility_off' : 'visibility'"
                            class="cursor-pointer"
                            @click="toggle_NewPassword = !toggle_NewPassword"
                        />
                    </template>
                </q-input>
                <q-input v-model="model.confirmPassword" filled :type="toggle_ConfirmPassword ? 'password' : 'text'"
                         lazy-rules
                         :rules="
                             [
                                 (val) => !!val || $errorMessages.REQUIRED,
                                 (val) => val === model.newPassword || 'Не совпадает с новым паролем!',
                             ]"
                         label="Подтвердить пароль"
                >
                    <template v-slot:append>
                        <q-icon
                            :name="toggle_ConfirmPassword ? 'visibility_off' : 'visibility'"
                            class="cursor-pointer"
                            @click="toggle_ConfirmPassword = !toggle_ConfirmPassword"
                        />
                    </template>
                </q-input>
                <q-btn
                    color="positive"
                    unelevated
                    label="Сохранить"
                    type="submit"
                />

                <q-btn
                    color="negative"
                    unelevated
                    label="Закрыть"
                    :to="isEmployee ? {name: 'EmployeeInfo'} : {name: 'ClientInfo'}"
                />
            </q-form>
        </div>
    </q-card>
</template>

<script>
import {mapActions} from 'vuex';
import * as validationHelpers from '../../../validation/helpers';
import {roleIdentifierMixin} from '../../../mixins/roleIdentifierMixin';

export default {
    name: 'Password',
    mixins: [roleIdentifierMixin],
    data() {
        return {
            model: {
                oldPassword: '',
                newPassword: '',
                confirmPassword: '',
            },
            toggle_OldPassword: true,
            toggle_NewPassword: true,
            toggle_ConfirmPassword: true,
        };
    },
    methods: {
        ...validationHelpers,
        ...mapActions({
            changePassword: 'user/changePassword',
        }),
        isFormInvalid() {
            return this.$refs.form.validate()
                .then((success) => {
                    return (success);
                });
        },
        async save() {
            const isValid = await this.isFormInvalid();

            if (!isValid) {
                return;
            }

            const payload = this.model;
            this.changePassword(payload).finally(() => {
                this.$router.push(this.isEmployee ? {name: 'EmployeeInfo'} : {name: 'ClientInfo'});
            });
        },
    },
};
</script>
<style>
.card-form{
    max-width: 500px;
    margin-bottom: 50px
}
</style>
