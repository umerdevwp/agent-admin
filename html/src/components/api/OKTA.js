export const fetchUserProfile = async (sub) => {

    const response = await fetch(process.env.REACT_APP_OKTA_BASE_URL + `api/v1/users/${sub}`, {
        headers: {
            'Access-Control-Allow-Origin': '*',
            Accept: 'application/json',
            Authorization: `SSWS ${process.env.REACT_APP_OKTA_API_TOKEN}`,
        }
    });
    return Promise.resolve(response.json());


}
