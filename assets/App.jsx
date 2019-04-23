import React, { Fragment } from 'react'
import { connect } from 'react-redux'
import Notifier from 'components/Notifier/Notifier'

import { Route, Router, Switch } from 'react-router-dom'

import Homepage from 'views/Homepage/Homepage.jsx'
import ProfilePage from 'views/ProfilePage/ProfilePage.jsx'
import RegisterPage from 'views/RegisterPage/RegisterPage.jsx'
import LoginPage from 'views/LoginPage/LoginPage.jsx'
import { createBrowserHistory } from 'history'

const hist = createBrowserHistory(),
    App = () => {
        return (
            <>
                <Notifier />
                <Router history={hist}>
                    <Switch>
                        <Route path="/profile" exact component={ProfilePage}
                        />
                        <Route
                            component={RegisterPage}
                            exact
                            path="/register"
                        />
                        <Route component={LoginPage} exact path="/login" />
                        <Route path="/" exact component={Homepage} />
                    </Switch>
                </Router>
            </>
        )
    }

function mapStateToProps(state) {
    const { auth } = state
    return { authentication: auth }
}

export default connect(mapStateToProps)(App)
