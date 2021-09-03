<template>
    <q-card
        v-if="userData"
        flat
        bordered
        class="my-card bg-grey-1"
    >
        <div class="card-header">
            <h3 class="text-h4">
                Мой профиль
            </h3>
        </div>
        <q-card-section>
            <q-separator />
            <div class="row items-center no-wrap">
                <div class="col">
                    <div class="text-subtitle2">
                        Фамилия: {{ userData.lastName }}
                    </div>
                    <div class="text-subtitle2">
                        Имя: {{ userData.firstName }}
                    </div>
                    <div class="text-subtitle2">
                        Отчество: {{ userData.patronymic }}
                    </div>
                    <div class="text-subtitle2">
                        Дата рождения: {{ userData.birthDate }}
                    </div>
                    <div class="text-subtitle2">
                        Телефон для связи: {{ userData.phoneNumber | phoneFilter }}
                    </div>
                    <div class="text-subtitle2">
                        Email: {{ userData.email }}
                    </div>
                    <div class="text-subtitle2">
                        Skype: {{ userData.skype }}
                    </div>
                    <div class="text-subtitle2">
                        WhatsApp: {{ userData.whatsapp }}
                    </div>
                    <div class="text-subtitle2">
                        СНИЛС: {{ userData.snils }}
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
                :to="{name: 'UserEdit'}"
            />
            <q-btn
                color="primary"
                unelevated
                label="Сменить пароль"
                :to="{name: 'PasswordEdit'}"
            />
        </q-card-actions>
    </q-card>
</template>

<script>
import {mapGetters} from 'vuex';
import phoneFilter from '../../utils/phoneFilter';

export default {
    name: 'UserInfo',
    filters: {phoneFilter},
    computed: {
        ...mapGetters({
            userData: 'user/userData',
        }),

    },
    data() {
        return {
            tabs: 'personal-data',
        };
    },
};
</script>
