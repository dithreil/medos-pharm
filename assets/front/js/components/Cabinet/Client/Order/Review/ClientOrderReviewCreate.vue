<template>
    <div>
        <h5 class="card-header card-header_title">
            Оставить отзыв
        </h5>
        <div style="padding: 20px">
            <div>Оценка: </div>
            <q-rating
                v-model="model.rating"
                size="xl"
            />
        </div>
        <q-input
            type="textarea"
            outlined
            v-model="model.ratingComment"
            :placeholder="'Комменатрий к отзыву' +
                (model.rating < 4 && model.rating ? ' (обязателен к заполнению)' : '')"
        />
        <q-btn :disable="!readyToSend" @click="send">Опубликовать отзыв</q-btn>
    </div>
</template>

<script>
import {mapActions} from 'vuex';

export default {
    name: 'ClientOrderReviewCreate',
    props: {
        orderId: {
            required: true,
            type: String,
        },
    },
    data() {
        return {
            model: {
                action: 'action.rate',
                rating: 0,
                ratingComment: '',
            },
            readyToSend: true,
        };
    },
    methods: {
        ...mapActions({
            orderSpecialActions: 'order/orderSpecialActions',
        }),

        send() {
            this.readyToSend = false;
            this.orderSpecialActions({id: this.orderId, payload: this.model}).then(() => {
                this.readyToSend = true;
            });
        },
    },

};
</script>

