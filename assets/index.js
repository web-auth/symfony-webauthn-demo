import React from "react";
import ReactDOM from "react-dom";
import { createBrowserHistory } from "history";
import { Router, Route, Switch } from "react-router-dom";

import { SnackbarProvider } from "notistack";

import "assets/scss/material-kit-react.scss?v=1.4.0";

// pages for this product
import Homepage from "views/Homepage/Homepage.jsx";
import LandingPage from "views/LandingPage/LandingPage.jsx";
import RegisterPage from "views/RegisterPage/RegisterPage.jsx";
import LoginPage from "views/LoginPage/LoginPage.jsx";

var hist = createBrowserHistory();

ReactDOM.render(
  <SnackbarProvider maxSnack={3}>
    <Router history={hist}>
      <Switch>
        <Route path="/profile" exact={true} component={LandingPage} />
        <Route path="/register" exact={true} component={RegisterPage} />
        <Route path="/login" exact={true} component={LoginPage} />
        <Route path="/" exact={true} component={Homepage} />
      </Switch>
    </Router>
  </SnackbarProvider>,
  document.getElementById("root")
);
