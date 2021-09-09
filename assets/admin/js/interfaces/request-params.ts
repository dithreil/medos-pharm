export interface IRequestParams extends IPagination {
    filter: string | null,
}
export interface IPagination {
    [key: string]: string| boolean | number | null;
    descending: boolean | null,
    limit: number | null,
    page: number | null,
    rowsNumber: number | null,
    rowsPerPage: number | null,
    sortBy: string | null,
}

export interface IServerResponse {
    config: any,
    data: any,
    headers: any,
    request: XMLHttpRequest,
    status: number,
    statusText: string,
}
