import {categories} from '../constants';
import moment from 'moment';
import {mapActions, mapGetters} from 'vuex';

export const orderCreateMixin = {
    data() {
        return {
            model: {
                skype: '',
                whatsapp: '',
                area: null,
                speciality: null,
                category: null,
                date: null,
                client: null,
            },
            radioOptions: [
                {label: 'Skype', value: 'skype'},
                {label: 'WhatsApp', value: 'whatsapp'},
                {label: 'Приём в больнице', value: 'offline'},
            ],
            radioLoginType: 'skype',
            type: '',
            pagination: {
                sortBy: 'name',
                descending: false,
                page: 1,
                rowsPerPage: 10,
                rowsNumber: 0,
            },
            filterAreas: [],
            filterSpecialities: [],
        };
    },
    computed: {
        ...mapGetters({
            specialitiesData: 'speciality/specialitiesData',
        }),
        categories() {
            return categories;
        },
        specialitiesDataItems() {
            return !!this.specialitiesData?.items ? this.specialitiesData.items : this.specialitiesData;
        },
    },
    mounted() {
        this.model.category = this.categories[categories.length - 1];
        this.model.date = moment().format('YYYY-MM-DD');
    },
    methods: {
        ...mapActions({
            updateAreaRequestParams: 'area/updateAreaRequestParams',
            updateSpecialityRequestParams: 'speciality/updateSpecialityRequestParams',
        }),
        visitType() {
            return this.type = this.radioLoginType === 'offline' ? 'V' : 'I';
        },
        isFormInvalid() {
            return this.$refs.form.validate()
                .then((success) => {
                    return (success);
                });
        },
        fetchAreas(val, update, abort) {
            this.updateAreaRequestParams({pagination: this.pagination}).then(() => {
                this.filterAreas = this.areaData.items.filter((p) => {
                    return p.name.toUpperCase().includes(val.toUpperCase());
                });
            }).finally(() => {
                update();
            });
        },
        fetchSpecialities(val, update, abort) {
            this.updateSpecialityRequestParams(
                {pagination: this.pagination, filter: val, areaId: this.model.area.id}
            ).then(() => {
                this.filterSpecialities = this.specialitiesDataItems.filter((p) => {
                    return p.name.toUpperCase().includes(val.toUpperCase());
                });
            }).finally(() => {
                update();
            });
        },
    },
};
