import React, {Component} from 'react';
import {enqueueSnackbar} from 'app/store/actions/snackbarActions';
import {logout} from 'app/store/actions/authenticationActions';
import {Redirect} from 'react-router-dom';

import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';

class LogoutPage extends Component {
  componentDidMount = () => {
      fetch('/logout', {
          method: 'GET',
          credentials: 'same-origin',
          headers: {
              'Content-Type': 'application/json',
          },
      })
          .then(() => {
              this.props.enqueueSnackbar({
                  message:
                      'You are now logged out. See you later.',
              });
              this.props.logout();
          });
  };

  render() {
      return <Redirect to="/" />;
  }
}

const mapDispatchToProps = dispatch => {
    return bindActionCreators({
        enqueueSnackbar,
        logout,
    }, dispatch);
};

export default connect(null, mapDispatchToProps)(LogoutPage);
