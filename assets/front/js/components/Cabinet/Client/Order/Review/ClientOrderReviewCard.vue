<template>
    <div>
        <h5 class="card-header card-header_title">
            Отзыв:
        </h5>
        <q-card style="padding: 20px">
            <q-card-section>
                <div class="row items-center no-wrap">
                    <div class="col">
                        <div style="font-size: 18px; font-weight: 450">Оценка: </div>
                        <q-rating
                            :value="order.rating"
                            readonly
                        />
                    </div>
                    <div class="col-auto">
                        <q-btn color="grey-7" round flat icon="more_vert">
                            <q-menu cover auto-close>
                                <q-list>
                                    <q-item clickable>
                                        <q-item-section @click="cancel">Удалить отзыв</q-item-section>
                                    </q-item>
                                </q-list>
                            </q-menu>
                        </q-btn>
                    </div>
                </div>
            </q-card-section>

            <q-card-section >
                <div>{{order.ratingComment}}</div>
            </q-card-section>
        </q-card>
    </div>
</template>

<script>

export default {
    name: 'ClientOrderReviewCard',
    props: {
        order: {
            required: true,
            type: Object,
        },
    },
};
</script>

