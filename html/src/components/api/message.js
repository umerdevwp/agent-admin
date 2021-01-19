import {ENTITY} from "./enitity.crud";
import axios from 'axios';

export const sendMessageAPI = async (data) => {
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));

    if (okta) {
        // const response = await fetch(`${ENTITY}/message/send`, {
        //     method: 'post',
        //     headers: {
        //
        //         'Access-Control-Allow-Origin': '*',
        //         'Authorization': okta.accessToken.accessToken,
        //         'Content-Type': 'multipart/form-data',
        //     },
        //     body: data
        // });
        // return Promise.resolve(response.json());
    }
    if (okta) {
        const response = await axios.post(`${ENTITY}/message/send`, data, {
            headers: {
                'Access-Control-Allow-Origin': '*',
                'Authorization': okta.accessToken.accessToken,
                'content-type': 'multipart/form-data'
            },
        })
        return Promise.resolve(response.data);
    }



}


export const TemplateList = async (data) => {
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));

    if (okta) {
        const response = await fetch(`${ENTITY}/templates/list`, {
            method: 'get',
            headers: {
                'Access-Control-Allow-Origin': '*',
                'Authorization': okta.accessToken.accessToken,

            },
        });
        return Promise.resolve(response.json());
    }
}

export const getTemplate = async (data) => {
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));

    if (okta) {
        const response = await fetch(`${ENTITY}/templates/${data}`, {
            method: 'get',
            headers: {
                'Access-Control-Allow-Origin': '*',
                'Authorization': okta.accessToken.accessToken,

            },
        });
        return Promise.resolve(response.json());
    }
}


export const FetchThreads = async (data) => {
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));

    if (okta) {
        const response = await fetch(`${ENTITY}/message/list/${data}`, {
            method: 'get',
            headers: {
                'Access-Control-Allow-Origin': '*',
                'Authorization': okta.accessToken.accessToken,

            },
        });
        return Promise.resolve(response.json());
    }
}


export const sendMessageFormChat = async (data) => {
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));

    if (okta) {
        const response = await fetch(`${ENTITY}/message/send`, {
            method: 'post',
            headers: {
                'Access-Control-Allow-Origin': '*',
                'Authorization': okta.accessToken.accessToken,

            },
            body: data
        });
        return Promise.resolve(response.json());
    }
}


export const FetchMessageLogs = async (data) => {
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));

    if (okta) {
        const response = await fetch(`${ENTITY}/message/search`, {
            method: 'post',
            headers: {
                'Access-Control-Allow-Origin': '*',
                'Authorization': okta.accessToken.accessToken,

            },
            body: data
        });
        return Promise.resolve(response.json());
    }
}
