import React from "react";
import { enqueueSnackbar } from "store/snackbarActions";
// @material-ui/core components
import withStyles from "@material-ui/core/styles/withStyles";
import InputAdornment from "@material-ui/core/InputAdornment";
// @material-ui/icons
import Lock from "@material-ui/icons/Lock";
// core components
import Header from "app/components/Header/Header.jsx";
import HeaderLinks from "app/components/Header/HeaderLinks.jsx";
import Footer from "app/components/Footer/Footer.jsx";
import GridContainer from "components/Grid/GridContainer.jsx";
import GridItem from "components/Grid/GridItem.jsx";
import Button from "components/CustomButtons/Button.jsx";
import Card from "components/Card/Card.jsx";
import CardBody from "components/Card/CardBody.jsx";
import CardHeader from "components/Card/CardHeader.jsx";
import CardFooter from "components/Card/CardFooter.jsx";
import CustomInput from "components/CustomInput/CustomInput.jsx";

import {
  handlePublicKeyRequestOptions,
  handlePublicKeyRequestResult
} from "components/PublicKeyRequest/PublicKeyRequest.jsx";

import loginPageStyle from "assets/jss/material-kit-react/views/loginPage.jsx";

import image from "assets/img/bg7.jpg";
import { bindActionCreators } from "redux";
import { connect } from "react-redux";

import { withRouter } from "react-router";

class LoginPage extends React.Component {
  state = {
    cardAnimation: "cardHidden",
    isFormValid: false,
    username: "",
    isDeviceInteractionEnabled: false
  };

  handleUsernameChanged = event => {
    this.setState({
      username: event.target.value,
      isFormValid: event.target.value !== ""
    });
  };

  componentDidMount() {
    setTimeout(
      function() {
        this.setState({ cardAnimaton: "" });
      }.bind(this),
      400
    );
  }

  handleFormValidation = () => {
    handlePublicKeyRequestOptions(
      {
        username: this.state.username,
        displayName: this.state.displayname
      },
      this.handlePublicKeyRequestOptions__,
      this.loginFailureHandler
    );
  };

  handlePublicKeyRequestOptions__ = publicKeyRequestOptions => {
    this.setState({
      isDeviceInteractionEnabled: true
    });
    function arrayToBase64String(a) {
      return btoa(String.fromCharCode(...a));
    }

    publicKeyRequestOptions.challenge = Uint8Array.from(
      window.atob(publicKeyRequestOptions.challenge),
      c => {
        return c.charCodeAt(0);
      }
    );
    if (publicKeyRequestOptions.allowCredentials !== undefined) {
      publicKeyRequestOptions.allowCredentials = publicKeyRequestOptions.allowCredentials.map(
        data => {
          return {
            type: data.type,
            id: Uint8Array.from(atob(data.id), c => {
              return c.charCodeAt(0);
            })
          };
        }
      );
    }
    navigator.credentials
      .get({ publicKey: publicKeyRequestOptions })
      .then(data => {
        const publicKeyCredential = {
          id: data.id,
          type: data.type,
          rawId: arrayToBase64String(new Uint8Array(data.rawId)),
          response: {
            authenticatorData: arrayToBase64String(
              new Uint8Array(data.response.authenticatorData)
            ),
            clientDataJSON: arrayToBase64String(
              new Uint8Array(data.response.clientDataJSON)
            ),
            signature: arrayToBase64String(
              new Uint8Array(data.response.signature)
            ),
            userHandle: data.response.userHandle
              ? arrayToBase64String(new Uint8Array(data.response.userHandle))
              : null
          }
        };
        handlePublicKeyRequestResult(
          publicKeyCredential,
          this.loginSuccessHandler,
          this.loginFailureHandler
        );
      })
      .catch(this.loginFailureHandler);
  };

  loginFailureHandler = () => {
    this.props.enqueueSnackbar({
      message:
        "An error occurred during the login process. Please try again later."
    });
    this.setState({
      isDeviceInteractionEnabled: false
    });
  };

  loginSuccessHandler = json => {
    if (json.status !== undefined && "ok" === json.status) {
      this.props.enqueueSnackbar({
        message: "Your are now logged in!"
      });
      this.setState({
        isDeviceInteractionEnabled: false
      });
      this.props.history.push("/");
    } else {
      this.loginFailureHandler();
    }
  };

  render() {
    const { classes, ...rest } = this.props;
    return (
      <div>
        <Header
          absolute
          color="transparent"
          brand="Webauthn Demo"
          rightLinks={<HeaderLinks />}
          {...rest}
        />
        <div
          className={classes.pageHeader}
          style={{
            backgroundImage: "url(" + image + ")",
            backgroundSize: "cover",
            backgroundPosition: "top center"
          }}
        >
          <div className={classes.container}>
            <GridContainer justify="center">
              <GridItem xs={12} sm={12} md={4}>
                <Card className={classes[this.state.cardAnimaton]}>
                  <form className={classes.form}>
                    <CardHeader color="primary" className={classes.cardHeader}>
                      <h4>Login</h4>
                    </CardHeader>
                    <CardBody>
                      <CustomInput
                        labelText="Username..."
                        id="first"
                        formControlProps={{
                          fullWidth: true
                        }}
                        inputProps={{
                          onChange: event => this.handleUsernameChanged(event),
                          type: "text",
                          endAdornment: (
                            <InputAdornment position="end">
                              <Lock className={classes.inputIconsColor} />
                            </InputAdornment>
                          )
                        }}
                      />
                    </CardBody>
                    <CardFooter className={classes.cardFooter}>
                      <Button
                        simple
                        color="primary"
                        size="lg"
                        disabled={!this.state.isFormValid}
                        onClick={this.handleFormValidation}
                      >
                        Get started
                      </Button>
                    </CardFooter>
                  </form>
                </Card>
              </GridItem>
            </GridContainer>
          </div>
          <Footer whiteFont />
        </div>
      </div>
    );
  }
}

const mapDispatchToProps = dispatch =>
  bindActionCreators({ enqueueSnackbar }, dispatch);

export default withRouter(
  connect(
    null,
    mapDispatchToProps
  )(withStyles(loginPageStyle)(LoginPage))
);
