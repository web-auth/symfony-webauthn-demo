import React, { Component } from "react";
import { connect } from 'react-redux'
import { Route, Redirect } from 'react-router-dom'

class ProtectedRoute extends Component {
  render() {
    const { isAuthenticated, ...props} = this.props
    let route = <Redirect to='/' />
    if (isAuthenticated) {
      route = <Route {...props} />
    }
    return route
  }
}

function mapStateToProps(state) {
  const { auth } = state
  let isAuthenticated = false
  if (auth !== undefined && auth.data !== undefined && auth.data !== null) {
    isAuthenticated = true
  }
  return { isAuthenticated }
}

export default connect(mapStateToProps)(ProtectedRoute);
