<template>
    <q-layout>
        <q-page-container>
            <q-page class="column justify-start items-center bg-blue-8" style="padding-top: 10%;">
                <h1 class="text-h3 text-white">
                    Авторизация
                </h1>
                <q-form
                    ref="form"
                    class="q-gutter-sm col"
                    style="max-width: 500px"
                    autocomplete="off"
                    @submit="login"
                >
                    <q-card class="my-card q-py-sm shadow-3" style="min-width: 320px">
                        <q-card-section>
                            <q-input
                                v-model="model.username"
                                autofocus
                                outlined
                                label="Логин / Email"
                                lazy-rules
                                :rules="[
                                    (val) => !!val || 'Обязательное поле',
                                ]"
                            />

                            <q-input
                                v-model="model.password"
                                outlined
                                type="password"
                                label="Пароль"
                                lazy-rules
                                :rules="[
                                    (val) => !!val || 'Обязательное поле',
                                ]"
                            />
                        </q-card-section>

                        <q-card-section class="q-pt-none">
                            <q-btn
                                class="q-px-lg"
                                unelevated
                                color="primary"
                                label="Войти"
                                type="submit"
                            />
                        </q-card-section>
                    </q-card>
                </q-form>
            </q-page>
        </q-page-container>
    </q-layout>
</template>

<script>
import {requests, apiConstants} from '../api';
import * as notifizer from '../utils/notifizer';

export default {
    data() {
        return {
            model: {
                username: '',
                password: '',
                user_type: 'employee',
            },
        };
    },
    methods: {
        isFormInvalid() {
            return this.$refs.form.validate()
                .then((success) => {
                    return success;
                });
        },
        async login() {
            const isValid = await this.isFormInvalid();

            if (!isValid) {
                return;
            }

            requests.post(apiConstants.AUTH.LOGIN, {...this.model})
                .then((response) => {
                    if (response.status !== 200) {
                        notifizer.error();
                    } else {
                        window.location.href = '/admin';
                    }
                })
                .catch((error) => {
                    console.log(error);
                    notifizer.error(error.response.data.error);
                });
        },
    },
};
</script>
