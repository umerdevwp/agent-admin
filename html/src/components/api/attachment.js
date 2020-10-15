const HOST = process.env.REACT_APP_SERVER_API_URL;


export const ENTITY = HOST;


export const AttachmentsList = async (zoho_id) => {
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));

    if(okta) {
        const response = await fetch(`${ENTITY}/Documents/list/?eid=${zoho_id}`, {
            headers: {
                'Access-Control-Allow-Origin': '*',
                'Authorization': okta.accessToken.accessToken,

            }
        });
        return Promise.resolve(response.json());
    }
}


export const attachFiles = async (data) => {
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));
    if(okta) {
        const response = await fetch(`${ENTITY}/documents/attachment`, {
            method: 'post',
            headers: {
                'Access-Control-Allow-Origin': '*',
                'Authorization': okta.accessToken.accessToken,
            },
            body: data
        })
        return Promise.resolve(response.json());
    }
}


export const taskUpdate = async (eid, data) => {
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));

    if (okta) {

        const response = await fetch(`${ENTITY}/Tasks/completeTaskInZoho/${eid}`, {
            method: 'put',
            headers: {
                'Access-Control-Allow-Origin': '*',
                'Authorization': okta.accessToken.accessToken,

            },
            body: data
        });
        return Promise.resolve(response.json());
    }
}
