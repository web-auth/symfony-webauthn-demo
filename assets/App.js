import React, { Fragment } from "react";
import { connect } from "react-redux";
import Notifier from "components/Notifier/Notifier";

import { Router, Route, Switch } from "react-router-dom";

import Homepage from "views/Homepage/Homepage.jsx";
import ProfilePage from "views/ProfilePage/ProfilePage.jsx";
import RegisterPage from "views/RegisterPage/RegisterPage.jsx";
import LoginPage from "views/LoginPage/LoginPage.jsx";
import { createBrowserHistory } from "history";

const hist = createBrowserHistory();

const App = () => {
  return (
    <Fragment>
      <Notifier />
      <Router history={hist}>
        <Switch>
          <Route path="/profile" exact={true} component={ProfilePage} />
          <Route path="/register" exact={true} component={RegisterPage} />
          <Route path="/login" exact={true} component={LoginPage} />
          <Route path="/" exact={true} component={Homepage} />
        </Switch>
      </Router>
    </Fragment>
  );
};

function mapStateToProps(state) {
  const { auth } = state;
  return { authentication: auth };
}

export default connect(mapStateToProps)(App);
