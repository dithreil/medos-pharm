<template>
    <q-card
        v-if="userData"
        flat
        bordered
        class="my-card bg-grey-3 my-card"
    >
        <div class="card-header">
            <h4 class="card-header card-header_title">
                Мой профиль
            </h4>
            <q-separator />
        </div>
        <q-card-section>
            <div style="margin-left: 25%; width: 50%" class="row items-center no-wrap ">
                <div class="col">
                    <div class="col-table">
                        <div class="text-subtitle2 col-table_color">
                            Фамилия:
                        </div>
                        <span class="font-size">{{ userData.lastName }}</span>
                    </div>
                    <div class="col-table">
                        <div class="text-subtitle2 col-table_color">
                            Имя:
                        </div>
                        <span class="font-size">{{ userData.firstName }}</span>
                    </div>
                    <div class="col-table">
                        <div class="text-subtitle2 col-table_color">
                            Отчество:
                        </div>
                        <span class="font-size">{{ userData.patronymic }}</span>
                    </div>
                    <div v-if="isEmployee" class="col-table">
                        <div class="text-subtitle2 col-table_color">
                            Специализация
                        </div>
                        <span class="font-size">{{ userData.speciality }}</span>
                    </div>
                    <div class="col-table">
                        <div class="text-subtitle2 col-table_color">
                            Телефон для связи:
                        </div>
                        <span class="font-size">
                            {{ userData.phoneNumber | phoneFilter }}</span>
                    </div>
                    <div class="col-table">
                        <div class="text-subtitle2 col-table_color">
                            Email:
                        </div>
                        <span class="font-size">{{ userData.email }}</span>
                    </div>
                    <div v-if="!isEmployee">
                        <div class="col-table">
                            <div class="text-subtitle2  col-table_color">
                                Skype:
                            </div>
                            <span class="font-size col-table_color">{{ userData.skype }}</span>
                        </div>
                        <div class="col-table">
                            <div class="text-subtitle2 col-table_color">
                                WhatsApp:
                            </div>
                            <span class="font-size">{{ userData.whatsapp | phoneFilter }}</span>
                        </div>
                        <div class="col-table">
                            <div class="text-subtitle2 col-table_color">
                                СНИЛС:
                            </div>
                            <span class="font-size">{{ userData.snils }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <q-separator />
        </q-card-section>
        <q-card-actions>
            <q-btn
                color="primary"
                unelevated
                label="Редактировать"
                :to="isEmployee ? {name: 'EmployeeEdit'} : {name: 'ClientEdit'}"
                size="12px"
                class="shadow-10"
            />
            <q-btn
                color="secondary"
                unelevated
                label="Сменить пароль"
                :to="isEmployee ? {name: 'EmployeePasswordEdit'} : {name: 'ClientPasswordEdit'}"
                size="12px"
                class="shadow-10"
            />
        </q-card-actions>
    </q-card>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import phoneFilter from '../../../utils/phoneFilter';
import {roleIdentifierMixin} from '../../../mixins/roleIdentifierMixin';

export default {
    name: 'UserInfo',
    filters: {phoneFilter},
    mixins: [roleIdentifierMixin],
    computed: {
        ...mapGetters({
            userData: 'user/userData',
        }),
    },
    methods: {
        ...mapActions({
            getUserData: 'user/getUserData',
        }),
    },
    mounted() {
        this.getUserData();
    },
};
</script>
