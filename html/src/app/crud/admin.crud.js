import axios from "axios";

const HOST = process.env.REACT_APP_SERVER_API_URL
export const LOGIN_URL = "api/auth/login";
export const REGISTER_URL = "api/auth/register";
export const REQUEST_PASSWORD_URL = "api/auth/forgot-password";
export const ENTITY = HOST;


export const adminList = async (zoho_id, email) => {
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));


    if(okta) {
        const response = await fetch(`${ENTITY}/admin_api/adminlist/?zoho_id=${zoho_id}&email=${email}`, {
            headers: {
                'Access-Control-Allow-Origin': '*',
                'Authorization': okta.accessToken.accessToken,
            }
        });
        return Promise.resolve(response.json());
    }
}


export const adminCreate = async (data) => {
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));


    if(okta) {
        const response = await fetch(`${ENTITY}/admin_api/create`, {
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
