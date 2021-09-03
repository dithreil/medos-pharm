import axios from 'axios';
import apiConstants from './api-constants';

const instance = axios.create({
    baseURL: apiConstants.BASE,
    withCredentials: false,
    headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json charset=utf-8',
        'X-Requested-With': 'XMLHttpRequest',
    },
});

instance.interceptors.response.use((response) => {
    if (response.headers['content-type'] === 'text/html; charset=UTF-8' && response.request.responseURL) {
        window.location.href = response.request.responseURL;
    }

    return response;
}, (error) => {
    return Promise.reject(error);
});

const axiosParams = (params) => {
    const queries = {};

    for (const i in params) {
        if (params[i] !== '' && !Array.isArray(params[i])) {
            queries[i] = params[i];
        } else if (Array.isArray(params[i]) && params[i].length) {
            for (const param of params[i]) {
                queries[i] = param;
            }
        }
    }

    return new URLSearchParams(queries);
};

const requests = {
    get(url, queryParams = null) {
        if (queryParams) {
            return instance.get(url, {params: axiosParams(queryParams)});
        }

        return instance.get(url);
    },
    post(url, data) {
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
    put(url, data) {
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
    patch(url, data) {
        const config = {};
        if (data instanceof FormData) {
            config.headers = {
                'Content-Type': 'multipart/form-data',
            };
        }

        return instance.patch(url, data, config);
    },
    delete(url) {
        return instance.delete(url);
    },
};

export {requests, apiConstants};
