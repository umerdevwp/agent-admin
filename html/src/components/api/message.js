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
