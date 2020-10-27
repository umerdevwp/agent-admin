
const HOST = process.env.REACT_APP_SERVER_API_URL;

const LOREX_TOKEN = process.env.REACT_APP_LOREX_TOKEN;
const LOREX_API_HOST = process.env.REACT_APP_LOREX_API_HOST;



// const LOREX_TOKEN = '3cJe5YXiSBGUAqYd0uFC2cKYvgfBIUswmXTudN3HQfvzIGvddfVYjPmakGOkGVM9g5YRKJR2FF9iYuZQ0GsbGw';
// const LOREX_API_HOST = 'https://lorax-api-sandbox.filemystuff.com';
export const LOGIN_URL = "api/auth/login";
export const REGISTER_URL = "api/auth/register";
export const REQUEST_PASSWORD_URL = "api/auth/forgot-password";
export const ENTITY = HOST;



export const ME_URL = "api/me";

export const entityList = async (token) => {


    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));

    if(okta) {
        const response = await fetch(`${ENTITY}/Entity/getChildAccount`, {
            headers: {
                'Access-Control-Allow-Origin': '*',
                'Authorization': token,

            }
        });
        return Promise.resolve(response.json());
    }
}

export const checkRole = async (eid,bit, tokenOKTA) => {
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));
    var response = '';
    if(okta) {
        if(bit === 1) {
           response = await fetch(`${ENTITY}/entity/role?eid=${eid}&bit=${bit}`, {
                headers: {
                    'Access-Control-Allow-Origin': '*',
                    'Authorization': okta.accessToken.accessToken,

                }
            });
        }
        if(bit === 0) {
         response = await fetch(`${ENTITY}/entity/role?eid=${eid}`, {
                headers: {
                    'Access-Control-Allow-Origin': '*',
                    'Authorization': okta.accessToken.accessToken,

                }
            });
        }



        return Promise.resolve(response.json());
    }
}



export const checkAdmin = async (zoho_id, email) => {
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));

    if(okta) {
        const response = await fetch(`${ENTITY}/admin_api/checkadmin/?zoho_id=${zoho_id}&email=${email}`, {
            headers: {
                'Access-Control-Allow-Origin': '*',
                'Authorization': okta.accessToken.accessToken,

            }
        });
        return Promise.resolve(response.json());
    }
}


export const taskUpdate = async (eid, data) => {
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));

    if(okta) {
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

export const entityDetail = async (entity) => {
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));

    if(okta) {
        const response = await fetch(`${ENTITY}/entity/entityview/?eid=${entity}`, {
            headers: {
                'Access-Control-Allow-Origin': '*',
                'Authorization': okta.accessToken.accessToken,

            }
        });
        return Promise.resolve(response.json());
    }
}



export const selfEntityDetail = async () => {
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));

    if(okta) {
        const response = await fetch(`${ENTITY}/entity/entityview`, {
            headers: {
                'Access-Control-Allow-Origin': '*',
                'Authorization': okta.accessToken.accessToken,

            }
        });
        return Promise.resolve(response.json());
    }
}


export const ContactTypeList = async (eid, email) => {
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));
    if(okta) {
        const response = await fetch(`${ENTITY}/ContactTypes/list?eid=${eid}`, {
            headers: {
                'Access-Control-Allow-Origin': '*',
                'Authorization': okta.accessToken.accessToken,

            }
        });
        return Promise.resolve(response.json());
    }
}


export const EntityList = async (eid, email) => {
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));
    if(okta) {
        const response = await fetch(`${ENTITY}/entity/comboList`, {
            headers: {
                'Access-Control-Allow-Origin': '*',
                'Authorization': okta.accessToken.accessToken,

            }
        });
        return Promise.resolve(response.json());
    }
}

export const EntitytypesList = async (eid, email) => {
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));
    if(okta) {
        const response = await fetch(`${ENTITY}/EntityTypes/list?eid=${eid}`, {
            headers: {
                'Access-Control-Allow-Origin': '*',
                'Authorization':okta.accessToken.accessToken,
            }
        });
        return Promise.resolve(response.json());
    }
}


export const StateRegionList = async (eid, email) => {
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));

    if(okta) {
        const response = await fetch(`${ENTITY}/States/list?eid=${eid}`, {
            headers: {
                'Access-Control-Allow-Origin': '*',
                'Authorization': okta.accessToken.accessToken,
            }
        });
        return Promise.resolve(response.json());
    }
}



export const createEntity = async (data) => {
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));
    if(okta) {
        const response = await fetch(`${ENTITY}/entity/create`, {
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

export const lorexFileUpload = async (data) => {


    // console.log('LOREX_TOKEN',LOREX_TOKEN);
    // console.log('LOREX_URL',`${LOREX_API_HOST}/api/v1/upload`);
    // console.log('LOREX_URL',HOST);
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));
    if(okta) {
        const response = await fetch(`${LOREX_API_HOST}/api/v1/upload`, {
            method: 'post',
            headers: {
                'authorization': LOREX_TOKEN,
            },
            body: data
        })
        return Promise.resolve(response.json());
    }
}



export const testcall = async (data) => {
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));
    if(okta) {
        const response = await fetch('https://apidev.youragentservices.com/api/entity/add', {
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




