import React, {Component} from 'react';
import {enqueueSnackbar} from 'app/store/actions/snackbarActions';
import {authSuccess} from 'app/store/actions/authenticationActions';
// @material-ui/core components
import withStyles from '@material-ui/core/styles/withStyles';
import InputAdornment from '@material-ui/core/InputAdornment';
// @material-ui/icons
import Lock from '@material-ui/icons/Lock';
// core components
import SecurityLayout from 'app/layouts/SecurityLayout/SecurityLayout.jsx';
import Button from 'components/CustomButtons/Button.jsx';
import CardBody from 'components/Card/CardBody.jsx';
import CardHeader from 'components/Card/CardHeader.jsx';
import CardFooter from 'components/Card/CardFooter.jsx';
import CustomInput from 'components/CustomInput/CustomInput.jsx';

import loginPageStyle from 'assets/jss/material-kit-react/views/loginPage.jsx';

import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';

import {withRouter} from 'react-router';
import SecurityKey from 'app/img/securitykey.min.svg';

import {useLogin} from 'webauthn-helper';

class LoginPage extends Component {
  state = {
    cardAnimation: 'cardHidden',
    isFormValid: false,
    username: '',
    isDeviceInteractionEnabled: false,
  };

  handleUsernameChanged = event => {
    event.preventDefault()
    this.setState({
      username: event.target.value,
      isFormValid: event.target.value !== '',
    });
  };

  cardAnimation = () => {
    this.setState({cardAnimation: ''});
  };

  componentDidMount = () => {
    setTimeout(this.cardAnimation, 700);
  };

  handleKeyPressed = event => {
    if (event.which === 13) {
      this.handleFormValidation(event)
    }
  };


  loginFailureHandler = () => {
    this.props.enqueueSnackbar({
      message:
        'An error occurred during the login process. Please try again later.',
    });
    this.setState({
      isDeviceInteractionEnabled: false,
    });
  };

  loginSuccessHandler = json => {
    if (json.status !== undefined && json.status === 'ok') {
      this.props.enqueueSnackbar({
        message: 'Your are now logged in!',
      });
      this.props.authSuccess(json);
      this.setState({
        isDeviceInteractionEnabled: false,
      });
      this.props.history.push('/');
    } else {
      this.loginFailureHandler(json.errorMessage);
    }
  };

  handleLoginProcess = useLogin({
    actionUrl: '/api/login',
    optionsUrl: '/api/login/options',
  });

  handleFormValidation = event => {
    event.preventDefault()
    this.handleLoginProcess({
      username: this.state.username,
    })
      .then(json => this.loginSuccessHandler(json))
      .catch(err => this.loginFailureHandler(err));
    ;
  };

  render() {
    const {classes} = this.props;
    let cardBody = (
      <form className={classes.form}>
        <CardHeader color="primary" className={classes.cardHeader}>
          <h4>Authentication</h4>
        </CardHeader>
        <CardBody>
          <p>
            Please enter your username and submit the form.
          </p>
          <CustomInput
            labelText="Username"
            id="username"
            formControlProps={{
              fullWidth: true,
            }}
            inputProps={{
              onKeyPress: event => this.handleKeyPressed(event),
              onChange: event => this.handleUsernameChanged(event),
              type: 'text',
              value: this.state.username,
              endAdornment: (
                <InputAdornment position="end">
                  <Lock className={classes.inputIconsColor} />
                </InputAdornment>
              ),
            }}
          />
        </CardBody>
        <CardFooter className={classes.cardFooter}>
          <Button simple color="primary" size="lg" disabled={!this.state.isFormValid} onClick={event => this.handleFormValidation(event)}>
            Submit
          </Button>
        </CardFooter>
      </form>
    );
    if (this.state.isDeviceInteractionEnabled) {
      cardBody = (
        <div>
          <CardHeader color="primary" className={classes.cardHeader}>
            <h4>Authentication</h4>
          </CardHeader>
          <CardBody>
            <p>
              You should now be notified to tap your security device (button,
              bluetooth, NFC, fingerprint…).
            </p>
            <img src={SecurityKey} alt="Tap your device" width="100%" />
          </CardBody>
        </div>
      );
    }
    return (
      <SecurityLayout>
        { cardBody }
      </SecurityLayout>
    );
  }
}

const mapDispatchToProps = dispatch => {
  return bindActionCreators({
    enqueueSnackbar,
    authSuccess,
  }, dispatch);
};

export default withRouter(connect(null, mapDispatchToProps)(withStyles(loginPageStyle)(LoginPage)));
