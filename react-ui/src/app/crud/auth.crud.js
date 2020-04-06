import axios from "axios";

export const LOGIN_URL = "api/auth/login";
export const REGISTER_URL = "api/auth/register";
export const REQUEST_PASSWORD_URL = "api/auth/forgot-password";

export const ME_URL = "api/me";

export function login(email, password) {
  return axios.post(LOGIN_URL, { email, password });
}

export function register(email, fullname, username, password) {
  return axios.post(REGISTER_URL, { email, fullname, username, password });
}

export function requestPassword(email) {
  return axios.post(REQUEST_PASSWORD_URL, { email });
}

export function getUserByToken() {
  console.log("I AM RECEIVING DATA FROM LOGIN");
  var ax = axios.get(ME_URL).then(res => {
    console.log(res);

  });
  // Authorization head should be fulfilled in interceptor.
  return ax;
}

export const userInfoAPI = async (token) => {
  const response = await fetch(process.env.REACT_APP_OKTA_BASE_URL + 'oauth2/default/v1/userinfo', {
    headers: {
      Authorization: `Bearer ${token}`
    }
  });
  return Promise.resolve(response.json());
}


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




