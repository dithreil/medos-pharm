import {IPagination} from "../interfaces/request-params";

const selectPagination: IPagination = {
    descending: false,
    limit: 10,
    page: 1,
    rowsNumber: 10,
    rowsPerPage: null,
    sortBy: null,
};
export default {
    selectPagination,
}
