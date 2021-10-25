import {mountQuasar} from '@quasar/quasar-app-extension-testing-unit-jest';
import IncomeList from '../../../assets/admin/js/pages/Income/IncomeList.vue';
import Vuex from 'vuex';
import incomeData from '../test-data/incomeData';
import {createLocalVue} from '@vue/test-utils';
import {QBtn, QTable} from 'quasar';
import systemConstants from '../../../assets/admin/js/constants/systemConstants';
const localVue = createLocalVue();
localVue.use(Vuex);

describe('IncomeList', () => {
    let wrapper;
    let store;
    let actions;
    function mountComponent(store) {
        wrapper = mountQuasar(IncomeList, {
            quasar: {
                components: {
                    QTable, QBtn,
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
            'income/updateIncomeRequestParams': jest.fn(),
        };
        store = new Vuex.Store({
            actions,
            getters: {
                'income/incomesData': () => incomeData,
                'income/incomeRequestParams': () => null,
            },
        });
        mountComponent(store);
    });

    test('matches snapshot', () => {
        expect(wrapper.element).toMatchSnapshot();
    });

    test('tableData correct work ', async() => {
        expect(wrapper.vm.tableData).toBe(incomeData.items);

        store.getters = {
            'income/incomesData': () => null,
            'income/incomeRequestParams': () => null,
        };
        mountComponent(store);

        expect(wrapper.vm.tableData).toEqual([]);
    });

    test('fetchIncomes correct work ', async() => {
        expect(actions['income/updateIncomeRequestParams']).toBeCalledTimes(1);

        await wrapper.vm.fetchIncomes({pagination: systemConstants.selectPagination});


        expect(actions['income/updateIncomeRequestParams']).toBeCalledTimes(2);

        await wrapper.vm.fetchIncomes({pagination: systemConstants.selectPagination});

        expect(wrapper.vm.$data.pagination).toEqual({
            sortBy: null,
            descending: false,
            page: 1,
            rowsPerPage: 10,
            rowsNumber: 6,
            limit: 10,
        });
    });
});
