import { AUTH_SUCCESS, AUTH_LOGOUT } from '../actions/actionTypes'
import {updateObject} from 'app/shared/utility.js';

const initialState = {
    data: sessionStorage.getItem('authentication_data') ? JSON.parse(sessionStorage.getItem('authentication_data')) : null
};

const authSuccess = (state, action) => {
    return updateObject( state, {
        data: action.data
    } );
};

const authLogout = (state, action) => {
    return updateObject(state, { data: null });
};

const reducer = ( state = initialState, action ) => {
    switch ( action.type ) {
        case AUTH_SUCCESS: return authSuccess(state, action);
        case AUTH_LOGOUT: return authLogout(state, action);
        default:
            return state;
    }
};

export default reducer;
