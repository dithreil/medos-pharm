<template>
    <q-page>
        <div class="text-center">
            <h1 v-if="isForEmployee" class="text-h4 text-primary">
                Авторизация для сотрудников
            </h1>
            <h1 v-else class="text-h2 text-primary">
                Авторизация
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
                    v-model="model.username"
                    outlined
                    label="Email"
                    type="email"
                    lazy-rules
                    autofocus
                    :rules="[
                        (val) => emailRule(val) || $errorMessages.INVALID_EMAIL,
                        (val) => !!val || $errorMessages.REQUIRED,
                    ]"
                />

                <q-input
                    v-model="model.password"
                    outlined
                    type="password"
                    label="Пароль"
                    lazy-rules
                    :rules="[
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
                        :to="{name: 'PasswordRestore', params: {userType}}"
                        class="q-mb-sm text-primary"
                    >
                        Забыли пароль?
                    </router-link>
                </div>

                <q-btn
                    color="primary"
                    size="md"
                    label="Войти"
                    type="submit"
                />
            </q-form>
        </div>
    </q-page>
</template>

<script>
import {mapActions} from 'vuex';
import * as validationHelpers from '../../../../admin/js/validation/helpers';

export default {
    name: 'Authorization',
    props: {
        userType: {
            type: String,
            default: 'client',
        },
    },
    data() {
        return {
            model: {
                username: '',
                password: '',
                user_type: this.userType,
            },
        };
    },
    computed: {
        isForEmployee() {
            return this.userType === 'employee';
        },
    },
    created() {
        if (['client', 'employee'].indexOf(this.userType) === -1) {
            this.$router.push({name: 'Authorization', params: {userType: 'client'}});
        }
    },
    methods: {
        ...validationHelpers,
        ...mapActions({
            login: 'authentication/login',
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

            this.login(this.model).then(() => this.$router.go());
        },
    },
};
</script>
