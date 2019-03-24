import React, { Fragment } from "react";
import Notifier from "components/Notifier/Notifier";

import { Router, Route, Switch } from "react-router-dom";

import Homepage from "views/Homepage/Homepage.jsx";
import LandingPage from "views/LandingPage/LandingPage.jsx";
import RegisterPage from "views/RegisterPage/RegisterPage.jsx";
import LoginPage from "views/LoginPage/LoginPage.jsx";
import { createBrowserHistory } from "history";

const hist = createBrowserHistory();

const App = () => (
  <Fragment>
    <Notifier />
    <Router history={hist}>
      <Switch>
        <Route path="/profile" exact={true} component={LandingPage} />
        <Route path="/register" exact={true} component={RegisterPage} />
        <Route path="/login" exact={true} component={LoginPage} />
        <Route path="/" exact={true} component={Homepage} />
      </Switch>
    </Router>
  </Fragment>
);

export default App;
