import {ENTITY} from "./enitity.crud";

export const sendMessageAPI = async (data) => {
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));

    if(okta) {
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


export const TemplateList = async (data) => {
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));

    if(okta) {
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

    if(okta) {
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

    if(okta) {
        const response = await fetch(`${ENTITY}/message/list/4071993000001842131`, {
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

    if(okta) {
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

    if(okta) {
        const response = await fetch(`${ENTITY}/message/search`, {
            method: 'get',
            headers: {
                'Access-Control-Allow-Origin': '*',
                'Authorization': okta.accessToken.accessToken,
            },
        });
        return Promise.resolve(response.json());
    }
}
