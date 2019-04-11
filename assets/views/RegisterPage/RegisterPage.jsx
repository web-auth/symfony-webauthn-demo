import React, { Component } from "react";
// @material-ui/core components
import withStyles from "@material-ui/core/styles/withStyles";
import InputAdornment from "@material-ui/core/InputAdornment";

import { connect } from "react-redux";
import { bindActionCreators } from "redux";
import { enqueueSnackbar } from "store/snackbarActions";

// @material-ui/icons
import { Lock, Face } from "@material-ui/icons";

// core components
import Header from "components/Header/Header.jsx";
import HeaderLinks from "components/Header/HeaderLinks.jsx";
import Footer from "components/Footer/Footer.jsx";
import GridContainer from "components/Grid/GridContainer.jsx";
import GridItem from "components/Grid/GridItem.jsx";
import Button from "components/CustomButtons/Button.jsx";
import Card from "components/Card/Card.jsx";
import CardBody from "components/Card/CardBody.jsx";
import CardHeader from "components/Card/CardHeader.jsx";
import CardFooter from "components/Card/CardFooter.jsx";
import CustomInput from "components/CustomInput/CustomInput.jsx";

import {
  handlePublicKeyCreationOptions,
  handlePublicKeyCreationResult
} from "components/PublicKeyCreation/PublicKeyCreation.jsx";

import loginPageStyle from "assets/jss/material-kit-react/views/loginPage.jsx";

import image from "assets/img/bg7.jpg";
import securitykey from "assets/img/securitykey.min.svg";

class RegisterPage extends Component {
  state = {
    cardAnimation: "cardHidden",
    isFormValid: false,
    username: "",
    displayname: "",
    isDeviceInteractionEnabled: false
  };

  componentDidMount = () => {
    // we add a hidden class to the card and after 700 ms we delete it and the transition appears
    setTimeout(() => {
      this.setState({ cardAnimation: "" });
    }, 400);
  };

  handleUsernameChanged = event => {
    this.setState({
      username: event.target.value,
      isFormValid: event.target.value !== "" && this.state.displayname !== ""
    });
  };

  handleDisplaynameChanged = event => {
    this.setState({
      displayname: event.target.value,
      isFormValid: this.state.username !== "" && event.target.value !== ""
    });
  };

  handleFormValidation = () => {
    handlePublicKeyCreationOptions(
      {
        username: this.state.username,
        displayName: this.state.displayname
      },
      this.handlePublicKeyCreationOptions__,
      () => {
        this.props.enqueueSnackbar({
          message:
            "An error occurred during the registration process. Please try again later."
        });
        this.setState({
          isDeviceInteractionEnabled: false
        });
      }
    );
  };

  handlePublicKeyCreationOptions__ = publicKeyCreationOptions => {
    this.setState({
      isDeviceInteractionEnabled: true
    });
    function arrayToBase64String(a) {
      return btoa(String.fromCharCode(...a));
    }

    publicKeyCreationOptions.challenge = Uint8Array.from(
      window.atob(publicKeyCreationOptions.challenge),
      c => {
        return c.charCodeAt(0);
      }
    );
    publicKeyCreationOptions.user = {
      ...publicKeyCreationOptions.user,
      id: Uint8Array.from(window.atob(publicKeyCreationOptions.user.id), c => {
        return c.charCodeAt(0);
      })
    };

    if (publicKeyCreationOptions.excludeCredentials !== undefined) {
      publicKeyCreationOptions.excludeCredentials = publicKeyCreationOptions.excludeCredentials.map(
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

    navigator.credentials.create({ publicKey: publicKeyCreationOptions }).then(
      data => {
        const publicKeyCredential = {
          id: data.id,
          type: data.type,
          rawId: arrayToBase64String(new Uint8Array(data.rawId)),
          response: {
            clientDataJSON: arrayToBase64String(
              new Uint8Array(data.response.clientDataJSON)
            ),
            attestationObject: arrayToBase64String(
              new Uint8Array(data.response.attestationObject)
            )
          }
        };
        handlePublicKeyCreationResult(
          publicKeyCredential,
          json => {
            if (json.status !== undefined && "ok" === json.status) {
              this.props.enqueueSnackbar({
                message: "Your account have been successfully created!"
              });
              this.setState({
                isDeviceInteractionEnabled: false
              });
              this.setState({
                isDeviceInteractionEnabled: false
              });
            } else {
              this.props.enqueueSnackbar({
                message:
                  "An error occurred during the registration process. Please try again later."
              });
              this.setState({
                isDeviceInteractionEnabled: false
              });
            }
          },
            () => {
            this.props.enqueueSnackbar({
              message:
                "An error occurred during the registration process. Please try again later."
            });
            this.setState({
              isDeviceInteractionEnabled: false
            });
          }
        );
      },
        () => {
        this.props.enqueueSnackbar({
          message:
            "An error occurred during the registration process. Please try again later."
        });
        this.setState({
          isDeviceInteractionEnabled: false
        });
      }
    );
  };

  render() {
    const { classes, ...rest } = this.props;

    let cardBody = (
      <form className={classes.form}>
        <CardHeader color="primary" className={classes.cardHeader}>
          <h4>Create an account</h4>
        </CardHeader>
        <CardBody>
          <p>
            Want to see how Webauthn will make you a better life? Just create an
            account by filling the form below.
          </p>
          <CustomInput
            labelText="Username"
            id="username"
            formControlProps={{
              fullWidth: true
            }}
            inputProps={{
              onChange: event => this.handleUsernameChanged(event),
              type: "text",
              value: this.state.username,
              endAdornment: (
                <InputAdornment position="end">
                  <Lock className={classes.inputIconsColor} />
                </InputAdornment>
              )
            }}
          />
          <CustomInput
            labelText="Display name"
            id="display_name"
            formControlProps={{
              fullWidth: true
            }}
            inputProps={{
              onChange: event => this.handleDisplaynameChanged(event),
              type: "text",
              value: this.state.displayname,
              endAdornment: (
                <InputAdornment position="end">
                  <Face className={classes.inputIconsColor} />
                </InputAdornment>
              )
            }}
          />
          <i>
            This is just a demo application, we don’t analyze or sell the
            information you will provide. Everything is deleted each month.
          </i>
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
    );
    if (this.state.isDeviceInteractionEnabled) {
      cardBody = (
        <div>
          <CardHeader color="primary" className={classes.cardHeader}>
            <h4>Create an account</h4>
          </CardHeader>
          <CardBody>
            <p>
              You should now be notified to tap your security device (button,
              bluetooth, NFC, fingerprint…).
            </p>
            <img src={securitykey} alt="Tap your device" width="100%" />
          </CardBody>
        </div>
      );
    }

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
                <Card className={classes[this.state.cardAnimation]}>
                  {cardBody}
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

export default connect(
  null,
  mapDispatchToProps
)(withStyles(loginPageStyle)(RegisterPage));
