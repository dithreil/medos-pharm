export const changePaginationKeys = (pagination, totalValue) => {
    const clonedPaginationData = JSON.parse(JSON.stringify(pagination));

    if (clonedPaginationData.rowsPerPage === 0) {
        clonedPaginationData.rowsPerPage = totalValue;
    }

    Object.defineProperty(
        clonedPaginationData,
        'limit',
        Object.getOwnPropertyDescriptor(clonedPaginationData, 'rowsPerPage')
    );

    return clonedPaginationData;
};
