import { AUTH_SUCCESS, AUTH_LOGOUT } from './actionTypes'

export const authSuccess = (data) => {
    sessionStorage.setItem('authentication_data', JSON.stringify(data));
    return {
        type: AUTH_SUCCESS,
        data: data
    };
};

export const logout = () => {
    sessionStorage.removeItem('authentication_data');
    return {
        type: AUTH_LOGOUT
    };
};
