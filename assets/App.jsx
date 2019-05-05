import React, { Fragment } from 'react'
import { connect } from 'react-redux'
import Notifier from 'app/components/Notifier/Notifier'

import { Route, Redirect, Router, Switch } from 'react-router-dom'

import AuthenticationChecker from 'app/components/AuthenticationChecker/AuthenticationChecker'
import ProtectedRoute from 'app/components/ProtectedRoute/ProtectedRoute'
import Homepage from 'app/views/Homepage/Homepage.jsx'
import ProfilePage from 'app/views/ProfilePage/ProfilePage.jsx'
import RegisterPage from 'app/views/RegisterPage/RegisterPage.jsx'
import LoginPage from 'app/views/LoginPage/LoginPage.jsx'
import { createBrowserHistory } from 'history'

const hist = createBrowserHistory(),
    App = () => {
        return (
            <Fragment>
                <AuthenticationChecker />
                <Notifier />
                <Router history={ hist }>
                    <Switch>
                        <ProtectedRoute path="/profile" exact component={ ProfilePage } />
                        <Route
                            component={ RegisterPage }
                            exact
                            path="/register"
                        />
                        <Route component={ LoginPage } exact path="/login" />
                        <Route path="/" exact component={ Homepage } />
                        <Redirect to='/' />
                    </Switch>
                </Router>
            </Fragment>
        )
    }

function mapStateToProps( state ) {
    const { auth } = state
    return { authentication: auth }
}

export default connect( mapStateToProps )( App )
