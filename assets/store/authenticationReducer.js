import { LOGIN, LOGOUT } from './actions'

const defaultState = {
    authentication: null,
}

export default (state = defaultState, action) => {
    switch (action.type) {
        case LOGIN:
            return {
                ...state,
                authentication: action.authenticationData,
            }

        case LOGOUT:
            return {
                ...state,
                authentication: null,
            }

        default:
            return state
    }
}
