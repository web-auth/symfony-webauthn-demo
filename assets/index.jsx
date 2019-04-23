import React from 'react'
import ReactDOM from 'react-dom'

import { SnackbarProvider } from 'notistack'

import 'assets/scss/material-kit-react.scss?v=1.4.0'

import { combineReducers, createStore } from 'redux'
import { Provider } from 'react-redux'
import snackbarReducer from 'store/snackbarReducer'
import authenticationReducer from 'store/authenticationReducer'
import App from './App'

const store = createStore(
    combineReducers({
        app: snackbarReducer,
        auth: authenticationReducer,
    })
)

ReactDOM.render(
    <Provider store={store}>
        <SnackbarProvider maxSnack={3}>
            <App />
        </SnackbarProvider>
    </Provider>,
    document.getElementById('root')
)
