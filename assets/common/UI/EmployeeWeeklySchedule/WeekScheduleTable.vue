<template>
    <div>
        <div class="coloring">
            <div v-for="item in coloringItems" :key="item.color" class="coloring__item">
                <span :style="{background: item.color}" /><span>{{item.descr}}</span>
            </div>
        </div>
        <table class="week-schedule-table">
            <tr style="border-bottom: #DADADA 1px solid" class="week-schedule-header">
                <th>Специализация</th>
                <th>Врач</th>
                <th>Пн</th>
                <th>Вт</th>
                <th>Ср</th>
                <th>Чт</th>
                <th>Пт</th>
                <th>Сб</th>
                <th>Вс</th>
            </tr>
            <WeekScheduleRow
                v-for="(row) in data"
                :key="row.id"
                :loaded="loaded"
                :row="row"
                @chosen="fetchDaySchedule"
            />
        </table>
        <q-dialog v-model="isDailyVisible" transition-show="rotate" transition-hide="rotate">
            <DailySchedule
                v-if="isDailyVisible"
                :day-info="dayInfo"
                :type="type"
                :clientId="clientId"
                @close="toggleVisibilityDailyModal"
                @order:created="isDailyVisible = false"
            />
        </q-dialog>
    </div>
</template>

<script>
import WeekScheduleRow from './WeekScheduleRow';
import {mapActions, mapGetters} from 'vuex';
import DailySchedule from './DailySchedule';

export default {
    name: 'WeekScheduleTable',
    components: {DailySchedule, WeekScheduleRow},
    props: {
        data: {
            required: true,
            type: Array,
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
            isDailyVisible: false,
            dayInfo: null,
            loaded: true,
            coloringItems: [
                {color: '#0064A1', descr: 'Доступно для записи'},
                {color: '#93BBD3', descr: 'Запись завершена'},
                {color: '#D2D2D2', descr: 'Нет приёма'},
            ],
        };
    },
    computed: {
        ...mapGetters({
            currentCategory: 'category/currentCategory',
        }),
    },
    methods: {
        ...mapActions({
            updateDayScheduleRequestParams: 'employee/updateDayScheduleRequestParams',
        }),
        fetchDaySchedule(payload) {
            this.loaded = false;
            this.updateDayScheduleRequestParams(
                {...payload.dataToServer, categoryCode: this.currentCategory.code}).then((response) => {
                this.dayInfo = payload.dayInfo;
                this.dayInfo.categoryId = this.currentCategory.id;
                this.toggleVisibilityDailyModal();
            }).finally(() => {
                this.loaded = true;
            });
        },
        toggleVisibilityDailyModal() {
            this.isDailyVisible = !this.isDailyVisible;
        },
    },
};
</script>
