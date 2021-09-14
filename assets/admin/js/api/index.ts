import axios from 'axios';
import apiConstants from './api-constants';

const instance = axios.create({
    baseURL: apiConstants.BASE,
    withCredentials: false,
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json charset=utf-8',
        'X-Requested-With': 'XMLHttpRequest',
    },
});

instance.interceptors.response.use((response) => {
    if ('text/html; charset=UTF-8' === response.headers['content-type'] && response.request.responseURL) {
        window.location.href = response.request.responseURL;
    }

    return response;
}, (error) => {
    return Promise.reject(error);
});

const axiosParams = (params: any) => {
    const queries = {};

    for (const i in params) {
        if (params[i]) {
            // @ts-ignore
            // todo: fix
            queries[i] = params[i];
        }
    }

    return new URLSearchParams(queries);
};

const requests = {
    get(url: string, queryParams = null) {
        if (!queryParams) {
            return instance.get(url);
        } else {
            return instance.get(url, {params: axiosParams(queryParams)});
        }
    },

    post(url: string, data: any) {
        const config = {
            headers: {
                'Content-Type': 'application/json',
            },
        };

        if (data instanceof FormData) {
            config.headers = {
                'Content-Type': 'multipart/form-data',
            };
        }

        return instance.post(url, data, config);
    },

    put(url: string, data: any) {
        const config = {
            headers: {
                'Content-Type': 'application/json',
            },
        };

        if (data instanceof FormData) {
            config.headers = {
                'Content-Type': 'multipart/form-data',
            };
        }

        return instance.put(url, data, config);
    },

    patch(url: string, data: any) {
        const config = {headers: {}};
        if (data instanceof FormData) {
            config.headers = {
                'Content-Type': 'multipart/form-data',
            };
        }

        return instance.patch(url, data, config);
    },

    delete(url: string) {
        return instance.delete(url);
    },
};

export {requests, apiConstants};
