<template>
    <q-dialog v-model="isActive">
        <q-card style="min-width: 320px">
            <q-card-section>
                <div class="text-h6">
                    Смена пароля
                </div>
            </q-card-section>

            <q-card-section class="q-pt-none">
                <q-input
                    ref="passwordInput"
                    v-model="password"
                    type="text"
                    autocomplete="off"
                    outlined
                    label="Новый пароль"
                    lazy-rules
                    :rules="[
                        (val) => val.length >= 4 || $errorMessages.INVALID_PASSWOR_LENGTH(4),
                        (val) => !!val || $errorMessages.REQUIRED,
                    ]"
                />
            </q-card-section>

            <q-card-actions align="right">
                <q-btn
                    v-close-popup
                    unelevated
                    label="Закрыть"
                    color="grey-5"
                />
                <q-btn
                    unelevated
                    label="Сохранить"
                    color="primary"
                    @click="save"
                />
            </q-card-actions>
        </q-card>
    </q-dialog>
</template>

<script>
import {mapActions} from 'vuex';
import {success} from '../../utils/notifizer';

export default {
    name: 'EmployeeChangePassword',
    data() {
        return {
            isActive: false,
            userId: '',
            password: '',
        };
    },
    methods: {
        ...mapActions({
            changeUserPassword: 'employee/changeUserPassword',
        }),
        toggleModalActivity(userId) {
            this.userId = userId;
            this.isActive = !this.isActive;
        },
        save() {
            if (!this.$refs.passwordInput.validate()) {
                return;
            }

            this.changeUserPassword({id: this.userId, password: this.password})
                .then((response) => {
                    if (response.status === 204) {
                        this.isActive = false;
                        success('Пароль успешно изменен');
                    }
                });
        },
    },
};
</script>
