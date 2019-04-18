import { LOGIN, LOGOUT } from "./actions";

export const login = authenticationData => ({
  type: LOGIN,
  authentication: authenticationData
});

export const logout = () => ({
  type: LOGOUT
});
