import {mountQuasar} from '@quasar/quasar-app-extension-testing-unit-jest';
import IncomeCreate from '../../../assets/admin/js/pages/Income/IncomeCreate.vue';
import Vuex from 'vuex';
import {createLocalVue} from '@vue/test-utils';
import {
    QBreadcrumbs,
    QBreadcrumbsEl,
    QBtn,
    QCard,
    QCardActions,
    QCardSection,
    QForm,
    QInput,
    QSelect,
    QTable,
} from 'quasar';
import systemConstants from '../../../assets/admin/js/constants/systemConstants';
const localVue = createLocalVue();
localVue.use(Vuex);

describe('IncomeCreate', () => {
    let wrapper;
    let store;
    let actions;
    function mountComponent(store) {
        wrapper = mountQuasar(IncomeCreate, {
            quasar: {
                components: {
                    QTable,
                    QBtn,
                    QBreadcrumbs,
                    QBreadcrumbsEl,
                    QCard,
                    QForm,
                    QCardSection,
                    QInput,
                    QSelect,
                    QCardActions,
                },
            },
            mount: {
                store,
                localVue,
            },
        });
    }
    beforeEach(async()=> {
        actions = {
            'income/postIncome': jest.fn(),
            'income/createIncome': jest.fn(),
            'store/updateStoreRequestParams': jest.fn(),
            'supplier/updateSupplierRequestParams': jest.fn(),
        };
        store = new Vuex.Store({
            actions,
            getters: {
                'supplier/suppliersData': () => null,
                'store/storesData': () => null,
            },
        });
        mountComponent(store);
    });

    test('matches snapshot', () => {
        expect(wrapper.element).toMatchSnapshot();
    });
});
