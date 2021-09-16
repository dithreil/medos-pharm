<template>
    <div v-if="nomenclature" class="employee-details">
      <q-breadcrumbs class="q-mb-xl employee-details__breadcrumbs">
        <q-breadcrumbs-el icon="home">
          <a class="q-breadcrumbs__el q-link" href="/admin">Панель Администратора</a>
        </q-breadcrumbs-el>
        <q-breadcrumbs-el
            label="Номенклатура"
            :to="{name: 'NomenclatureList'}"
        />
        <q-breadcrumbs-el
            :to="{name: 'NomenclatureDetails'}"
            :label="`${nomenclature.name}`"
        />
        <q-breadcrumbs-el
            class="employee-details__breadcrumbs employee-details__breadcrumbs_elStyle"
            label="Редактирование"
        />
      </q-breadcrumbs>
        <q-list
            class="employee-details__list"
            bordered
            separator
        >
            <q-item>
                <q-item-section>
                    <div class="row items-center">
                        <q-icon name="description" color="primary" size="sm" />
                        <span class="q-ml-sm">Наименование</span>
                    </div>
                </q-item-section>
                <q-item-section side>{{ nomenclature.name }}</q-item-section>
            </q-item>
            <q-item>
                <q-item-section>
                    <div class="row items-center">
                        <q-icon name="miscellaneous_services" color="primary" size="sm" />
                        <span class="q-ml-sm">Производитель</span>
                    </div>
                </q-item-section>
                <q-item-section side>{{ nomenclature.producer.shortName }}</q-item-section>
            </q-item>
          <q-item>
            <q-item-section>
              <div class="row items-center">
                <q-icon name="auto_awesome_motion" color="primary" size="sm" />
                <span class="q-ml-sm">Медицинская форма</span>
              </div>
            </q-item-section>
            <q-item-section side>{{ nomenclature.medicalForm }}</q-item-section>
          </q-item>
            <q-item>
                <q-item-section>
                    <div class="row items-center">
                        <q-icon name="done_outline" color="primary" size="sm" />
                        <span  class="q-ml-sm">НДС</span>
                    </div>
                </q-item-section>
                <q-item-section side>{{ nomenclature.isVat ? 'Да' : 'Нет' }}</q-item-section>
            </q-item>
        </q-list>
        <q-btn
            flat
            color="primary"
            label="Редактировать"
            :to="{name: 'NomenclatureEdit', params: {nomenclatureId: nomenclatureId}}"
        />
    </div>
</template>


<script lang="ts">
import {mapActions} from 'vuex';
import {Component, Prop, Vue} from 'vue-property-decorator';
import {INomenclatureDetails} from '../../interfaces/nomenclature';
import {nomenclatureCreate} from '../../models/CreateModels';
import {IServerResponse} from '../../interfaces/request-params';

@Component({
    methods: {
        ...mapActions({
            getNomenclatureDetails: 'nomenclature/getNomenclatureDetails',
        }),
    },
})
export default class NomenclatureDetails extends Vue {
    @Prop({type: String, required: true}) readonly nomenclatureId!: string;

    protected getNomenclatureDetails!: (id: string) => IServerResponse;

    protected nomenclature : INomenclatureDetails = JSON.parse(JSON.stringify(nomenclatureCreate));

    async mounted() {
        const response = await this.getNomenclatureDetails(this.nomenclatureId);
        this.nomenclature = response.data;
    }
};
</script>

