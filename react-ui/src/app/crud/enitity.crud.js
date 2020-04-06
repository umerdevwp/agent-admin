import axios from "axios";

const HOST = process.env.REACT_APP_SERVER_API_URL
export const LOGIN_URL = "api/auth/login";
export const REGISTER_URL = "api/auth/register";
export const REQUEST_PASSWORD_URL = "api/auth/forgot-password";
export const ENTITY = HOST;



export const ME_URL = "api/me";

export const entityList = async (zoho_id, email) => {
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));

    if(okta) {
        const response = await fetch(`${ENTITY}/user_api/userdata/?zoho_id=${zoho_id}&email=${email}`, {
            headers: {
                'Access-Control-Allow-Origin': '*',
                'Authorization': okta.idToken.idToken,

            }
        });
        return Promise.resolve(response.json());
    }
}

export const checkAdmin = async (zoho_id, email) => {
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));

    if(okta) {
        const response = await fetch(`${ENTITY}/admin_api/checkadmin/?zoho_id=${zoho_id}&email=${email}`, {
            headers: {
                'Access-Control-Allow-Origin': '*',
                'Authorization': okta.idToken.idToken,

            }
        });
        return Promise.resolve(response.json());
    }
}

export const entityDetail = async (zoho_id, email, entity) => {
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));

    if(okta) {
        const response = await fetch(`${ENTITY}/entity_api/entity/?zoho_id=${zoho_id}&email=${email}&entity=${entity}`, {
            headers: {
                'Access-Control-Allow-Origin': '*',
                'Authorization': okta.idToken.idToken,

            }
        });
        return Promise.resolve(response.json());
    }
}
