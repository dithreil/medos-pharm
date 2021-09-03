<template>
    <div class="client-notify" :style="{isEmployee: 'top: 32%'}">
        <template v-if="!isEmployee">
            <q-card v-if="notPaidOrdersData.length && isActive.notPaid" class="client-notify__item">
                <q-card-section>
                    <p class="text-negative text-subtitle1">У вас есть неоплаченные консультации!</p>
                </q-card-section>
                <q-card-actions align="center">
                    <q-btn size="sm" label="К консультациям" color="positive" @click="toOrders('notPaid')" />
                    <q-btn size="sm" label="Закрыть" color="negative" @click="close('notPaid')" />
                </q-card-actions>
            </q-card>
            <q-card v-if="forthcomingOrdersData.length && isActive.forthcoming" class="client-notify__item">
                <q-card-section>
                    <p class="text-negative text-subtitle1">У вас есть предстоящие консультации!</p>
                </q-card-section>
                <q-card-actions align="center">
                    <q-btn size="sm" label="К консультациям" color="positive" @click="toOrders('forthcoming')" />
                    <q-btn size="sm" label="Закрыть" color="negative" @click="close('forthcoming')" />
                </q-card-actions>
            </q-card>
            <q-card v-if="reviewsOrdersData.length && isActive.reviews" class="client-notify__item">
                <q-card-section>
                    <p class="text-negative text-subtitle1">У вас есть неоцененные консультации!</p>
                </q-card-section>
                <q-card-actions align="center">
                    <q-btn size="sm" label="К консультациям" color="positive" @click="toOrders('reviews')" />
                    <q-btn size="sm" label="Закрыть" color="negative" @click="close('reviews')" />
                </q-card-actions>
            </q-card>
        </template>
        <template v-else>
            <q-card v-if="forthcomingEmployeeOrders.length && isActive.employees" class="client-notify__item">
                <q-card-section>
                    <p class="text-negative text-subtitle1">У вас есть предстоящие консультации!</p>
                </q-card-section>
                <q-card-actions align="center">
                    <q-btn size="sm" label="К консультациям" color="positive" @click="toOrders('employees')" />
                    <q-btn size="sm" label="Закрыть" color="negative" @click="close('employees')" />
                </q-card-actions>
            </q-card>
        </template>
    </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import {roleIdentifierMixin} from '../../../../mixins/roleIdentifierMixin';

export default {
    name: 'UserNotifier',
    mixins: [roleIdentifierMixin],
    data() {
        return {
            ordersPagination: {
                page: 1,
                limit: 20,
            },
            isActive: {
                notPaid: false,
                forthcoming: false,
                reviews: false,
                employees: true,
            },
            forthcomingEmployeeOrders: [],
        };
    },
    computed: {
        ...mapGetters({
            forthcomingOrders: 'order/forthcomingOrdersData',
            notPaidOrders: 'order/notPaidOrdersData',
        }),
        forthcomingOrdersData() {
            return this.forthcomingOrders?.items ?? [];
        },
        notPaidOrdersData() {
            return this.notPaidOrders?.items ?? [];
        },
        reviewsOrdersData() {
            // return this.reviewsOrders?.items ?? []; // !FIXME

            return [];
        },
    },
    watch: {
        forthcomingOrdersData() {
            this.isActive.forthcoming = true;
        },
        notPaidOrdersData() {
            this.isActive.notPaid = true;
        },
        reviewsOrdersData() {
            this.isActive.reviews = true;
        },
    },
    mounted() {
        if (this.isEmployee) {
            this.getForthcomingOrders();

            return;
        }

        this.updateNotPaidOrderRequestParams(this.ordersPagination);
        this.updateForthcomingOrderRequestParams(this.ordersPagination);
    },
    methods: {
        ...mapActions({
            updateNotPaidOrderRequestParams: 'order/updateNotPaidOrderRequestParams',
            updateForthcomingOrderRequestParams: 'order/updateForthcomingOrderRequestParams',
        }),
        getForthcomingOrders() {
            this.updateForthcomingOrderRequestParams({page: this.ordersPagination.page,
                limit: this.ordersPagination.limit}).finally(() => {
                this.forthcomingEmployeeOrders = this.forthcomingOrdersData;
            });
        },
        toOrders(value) {
            this.close(value);
            this.$router.push({name: this.isEmployee ? 'EmployeeOrdersList' : 'ClientOrdersList'});
        },
        close(value) {
            this.isActive[value] = false;
        },
    },
};
</script>

<style lang="scss" scoped>
.client-notify {
    position: absolute;
    top: 40%;
    right: -10px;
    width: 250px;
    text-align: center;
    &__item {
        margin-bottom: 20px;
    }
}
</style>
