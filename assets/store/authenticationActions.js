import { LOGIN, LOGOUT } from './actions'

export const login = authenticationData => ({
    authentication: authenticationData,
    type: LOGIN,
})

export const logout = () => ({
    type: LOGOUT,
})
