<template>
    <q-page>
        <div class="text-center">
            <h1 class="text-h2 text-primary">
                Восстановление пароля
            </h1>
        </div>
        <div class="row justify-center items-center">
            <q-form
                ref="form"
                class="q-gutter-md col"
                style="max-width: 500px"
                @submit="send"
            >
                <q-input
                    v-model="email"
                    outlined
                    label="Email"
                    autofocus
                    lazy-rules
                    :rules="[
                        (val) => emailRule(val) || $errorMessages.INVALID_EMAIL,
                        (val) => !!val || $errorMessages.REQUIRED,
                    ]"
                />

                <div class="column justify-start items-start mb-4">
                    <router-link
                        v-if="!isForEmployee"
                        :to="{name: 'Registration'}"
                        class="q-mb-sm text-primary"
                    >
                        Регистрация
                    </router-link>
                    <router-link
                        :to="{name: 'Authorization', params: {userType}}"
                        class="q-mb-sm text-primary"
                    >
                        Авторизация
                    </router-link>
                </div>

                <q-btn
                    color="primary"
                    size="md"
                    label="Восстановить"
                    type="submit"
                />
            </q-form>
        </div>
    </q-page>
</template>

<script>
import {mapActions} from 'vuex';
import * as notifizer from '../../utils/notifizer';
import * as validationHelpers from '../../../../admin/js/validation/helpers';

export default {
    name: 'PasswordRestore',
    props: {
        userType: {
            type: String,
            default: 'client',
        },
    },
    data() {
        return {
            email: '',
        };
    },
    computed: {
        isForEmployee() {
            return this.userType === 'employee';
        },
    },
    created() {
        if (['client', 'employee'].indexOf(this.userType) === -1) {
            this.$router.push({name: 'PasswordRestore', params: {userType: 'client'}});
        }
    },
    methods: {
        ...validationHelpers,
        ...mapActions({
            restorePasswordClient: 'authentication/restorePasswordClient',
            restorePasswordEmployee: 'authentication/restorePasswordEmployee',
        }),
        isFormInvalid() {
            return this.$refs.form.validate()
                .then((success) => {
                    return success;
                });
        },
        async send() {
            const isValid = await this.isFormInvalid();

            if (!isValid) {
                return;
            }

            try {
                const payload = {email: this.email};
                this.isForEmployee
                    ? await this.restorePasswordEmployee(payload)
                    : await this.restorePasswordClient(payload);
            } catch (error) {
                console.log(error);
                notifizer.error();
            }
        },
    },
};
</script>
