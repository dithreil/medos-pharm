<template>
    <div class="modal">
        <h4>Выберите время посещения</h4>
        <p>Вы можете выбрать до 4х периодов, если они идут подряд.</p>
        <p>Специализация - {{dayInfo.specialityName}}</p>
        <p>Врач - {{dayInfo.employeeName}}</p>
        <div>
            <div
                v-for="(info, index) in daySchedule"
                :key="index + Date.now()"
                :style="{
                    backgroundColor: colors[index],
                    cursor: info.availability ? 'pointer': 'default'
                }"
                style="text-align: center; padding: 15px; border-radius: 5px; margin: 10px; display: inline-block;"
                @click="toggleSlotPicked(info)"
            >
                {{info.bTime}} - {{info.eTime}}
            </div>
        </div>
        <q-btn class="center" @click="makeAnOrder">Добавить консультацию</q-btn>
    </div>
</template>

<script>
import {mapGetters, mapActions} from 'vuex';
import moment from 'moment';
import {SCHEDULE_MAX_SLOTS} from '../../constants';
import ConsultationFeeDialog from './ConsultationFeeDialog.vue';
import {error} from '../../utils/notifizer';

export default {
    name: 'DailySchedule',
    props: {
        dayInfo: {
            required: true,
            type: Object,
        },
        type: {
            required: true,
            type: String,
        },
        clientId: {
            type: String,
        },
    },
    data() {
        return {
            slotsPicked: [],
            availableSlots: [],
            daySchedule: [],
            colors: [],
            colorTemplates: [
                '#D2D2D2',
                '#0064A1',
                'red',
            ],
            lastOrder: null,
        };
    },
    computed: {
        ...mapGetters({
            storeDaySchedule: 'employee/daySchedule',
            userData: 'user/userData',
        }),
        maxSlots() {
            return SCHEDULE_MAX_SLOTS;
        },
        pickedCount() {
            return this.slotsPicked.length || 0;
        },
    },
    mounted() {
        this.daySchedule = JSON.parse(JSON.stringify(this.storeDaySchedule));
        this.daySchedule.forEach((slot) => {
            slot.busy === 0 ? slot.availability = 1 : slot.availability = 0;
        });
        this.setColor();
    },
    methods: {
        ...mapActions({
            createOrder: 'order/createOrder',
            createPayment: 'payment/createPayment',
        }),
        toggleSlotPicked(slot) {
            if (!slot.availability) return;

            this.slotsPicked.includes(slot) ? this.slotReleased(slot) : this.slotPicked(slot);

            this.getAvailableSlots();
            this.setAvailability();
        },
        slotPicked(slot) {
            const slotIdDefault = this.daySchedule.indexOf(slot);
            this.slotsPicked.push(slot);
            this.slotsPicked.sort((p, b) => {
                return p.bTime > b.bTime ? 1 : p.bTime < b.bTime ? -1 : 0;
            });

            if (this.pickedCount > 1) {
                switch (this.slotsPicked.indexOf(slot)) {
                case 0:
                    for (let i = 1; this.slotsPicked[i - 1].eTime !== this.slotsPicked[i].bTime; i++) {
                        this.slotsPicked.splice(i, 0, this.daySchedule[slotIdDefault + i]);
                    }
                    break;
                case this.pickedCount - 1:
                    for (let i = 1; this.slotsPicked[this.pickedCount - i].bTime
                        !== this.slotsPicked[this.pickedCount - (i + 1)].eTime; i++) {
                        this.slotsPicked.splice(this.pickedCount - i, 0, this.daySchedule[slotIdDefault - i]);
                    }
                    break;
                }
            };
        },
        slotReleased(slot) {
            const pickedSlotIndex = this.slotsPicked.indexOf(slot);
            pickedSlotIndex >= this.pickedCount / 2
                ? this.slotsPicked = this.slotsPicked.slice(0, pickedSlotIndex)
                : this.slotsPicked = this.slotsPicked.slice(pickedSlotIndex + 1, this.pickedCount);
        },
        getAvailableSlots() {
            if (!this.pickedCount) {
                this.availableSlots = this.daySchedule;

                return;
            };

            this.availableSlots = [];

            const firstSlotIndex = this.daySchedule.indexOf(this.slotsPicked[0]);
            const isPreviousSlotAllow = this.daySchedule[firstSlotIndex - 1]?.busy === 0;
            const isNextSlotAllow = this.daySchedule[firstSlotIndex + 1]?.busy === 0;
            const previousSlots = isPreviousSlotAllow
                ? this.daySchedule.slice(
                    firstSlotIndex - (this.maxSlots - this.pickedCount), firstSlotIndex)
                : [];

            const lastSlotIndex = this.daySchedule.indexOf(this.slotsPicked[this.pickedCount - 1]);
            const nextSlots = isNextSlotAllow
                ? this.daySchedule.slice(
                    lastSlotIndex + 1, lastSlotIndex + ((this.maxSlots + 1) - this.pickedCount))
                : [];

            const nextBusySlotIndex = nextSlots.findIndex((slot) => slot.busy);
            const prevBusySlotIndex = previousSlots.findIndex((slot) => slot.busy);
            const filtredNextSlots = nextBusySlotIndex >= 0
                ? nextSlots.slice(0, nextBusySlotIndex)
                : nextSlots;
            const filtredPrevSlots = prevBusySlotIndex >= 0
                ? previousSlots.slice(previousSlots.length - prevBusySlotIndex)
                : previousSlots;

            this.availableSlots = [...filtredPrevSlots, ...filtredNextSlots];
        },
        setAvailability() {
            this.daySchedule.forEach((slot) => {
                slot.availability = 0;
                if (this.slotsPicked.includes(slot)) {
                    slot.availability = 2;
                } else if (this.availableSlots.length || this.pickedCount === this.maxSlots) {
                    slot.availability = this.availableSlots.includes(slot) && !slot.busy ? 1 : 0;
                };
                slot.color = this.colorTemplates[slot.availability];
            });
            this.setColor();
        },
        setColor() {
            this.colors = this.daySchedule.map((p) => {
                return this.colorTemplates[p.availability];
            });
        },
        async makeAnOrder() {
            const duration = this.slotsPicked.reduce((acc, item) => acc += item.duration, 0);
            const client = this.clientId ? this.clientId : this.userData.id;
            const formattedDate = moment(this.dayInfo.date, 'YYYY-MM-DD').format('DD.MM.YYYY');
            const startTime = `${formattedDate} ${this.slotsPicked[0].bTime}:00`;

            const dataToServer = {
                client,
                employee: this.dayInfo.employeeId,
                category: this.dayInfo.categoryId,
                startTime,
                cost: 700,
                type: this.type,
                duration,
                clientComment: 'string',
                employeeComment: 'string',
            };

            try {
                const {data} = await this.createOrder(dataToServer);
                this.lastOrder = data;

                if (this.clientId) {
                    this.$router.push({name: 'OrderList'});

                    return;
                }

                this.$q.dialog({
                    component: ConsultationFeeDialog,
                    parent: this,
                    date: startTime,
                    employee: this.dayInfo.employeeName,
                })
                    .onOk(async() => {
                        try {
                            const {data} = await this.createPayment({order: this.lastOrder.id});

                            window.location.href = data.confirmationUrl;
                        } catch (err) {
                            error('Что-то пошло не так!');
                        }
                    })
                    .onCancel(() => {
                        this.$router.push({name: 'ClientOrdersList'});
                    });
            } catch (error) {
                error('Невозможно создать консультацию на это время!');
            }
        },
    },
};
</script>
