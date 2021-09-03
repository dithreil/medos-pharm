<template>
    <tr class="week-schedule-row">
        <td>{{row.specialityName}}</td>
        <td>{{row.fullName}}</td>
        <td
            v-for="(day) in schedule"
            :key="day.id"
            style="color: #FFFFFF;  padding: 4px"
            :style="{
                backgroundColor: getColor(day),
                cursor: getCursor(day),
            }"
            @click="toDaySchedule(day)"
        >
            {{day.workTime}}
        </td>
    </tr>
</template>

<script>
import moment from 'moment';

export default {
    name: 'WeekScheduleRow',
    props: {
        row: {
            required: true,
            type: Object,
        },
        loaded: {
            default: true,
        },
    },
    computed: {
        schedule() {
            return this.row.weekSchedule.map((item) => {
                if (item.availability === 2) {
                    return Object.defineProperty(item, 'workTime', {
                        value: '',
                    });
                };

                return item;
            });
        },
    },
    methods: {
        isDatePast(date) {
            const yesterday = moment().subtract(1, 'days').format('YYYY-MM-DD');

            return !moment(yesterday).isBefore(date);
        },
        isWorkDay(time) {
            return !!time;
        },
        getColor(day) {
            if (this.isWorkDay(day.workTime)) {
                switch (day.availability) {
                case 2: return '#D2D2D2';
                case 1: return 'rgba(0, 100, 161, 0.4)';
                case 0: return '#0064A1';
                default: return 'black';
                }
            }

            return '#D2D2D2';
        },
        getCursor(day) {
            if (this.isWorkDay(day.workTime)) {
                if (day.availability === 0) return 'pointer';
            }

            return 'not-allowed';
        },
        toDaySchedule(day) {
            if (this.isDatePast(day.date)) return;
            if (day.availability !== 0) return;
            if (!this.isWorkDay(day.workTime)) return;

            const payload = {
                dataToServer: {
                    areaCode: this.row.areaCode,
                    specialityCode: this.row.specialityCode,
                    date: day.date,
                    employeeCode: this.row.code,
                },
                dayInfo: {
                    specialityName: this.row.specialityName,
                    employeeName: this.row.fullName,
                    employeeId: this.row.id,
                    date: day.date,
                },
            };

            this.$emit('chosen', payload);
        },
    },
};
</script>
