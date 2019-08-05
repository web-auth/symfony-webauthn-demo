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

import {
    handlePublicKeyRequestOptions,
    handlePublicKeyRequestResult,
} from 'app/components/PublicKeyRequest/PublicKeyRequest.jsx';

import loginPageStyle from 'assets/jss/material-kit-react/views/loginPage.jsx';

import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';

import {withRouter} from 'react-router';
import SecurityKey from 'app/img/securitykey.min.svg';

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

  handleFormValidation = event => {
      event.preventDefault()
      handlePublicKeyRequestOptions(
          {
              username: this.state.username,
          },
          this.handlePublicKeyRequestOptions__,
          this.loginFailureHandler
      );
  };

  handlePublicKeyRequestOptions__ = publicKeyRequestOptions => {
      this.setState({
          isDeviceInteractionEnabled: true,
      });

      function base64UrlDecode(input) {
          input = input
              .replace(/-/g, '+')
              .replace(/_/g, '/');

          const pad = input.length % 4;
          if (pad) {
              if (pad === 1) {
                  throw new Error('InvalidLengthError: Input base64url string is the wrong length to determine padding');
              }
              input += new Array(5-pad).join('=');
          }

          return window.atob(input);
      }

      publicKeyRequestOptions.challenge = Uint8Array.from(
          base64UrlDecode(publicKeyRequestOptions.challenge),
          c => c.charCodeAt(0)
      );

      /*publicKeyRequestOptions.challenge = Uint8Array.from(
          window.atob(publicKeyRequestOptions.challenge), c => c.charCodeAt(0));*/
      if (publicKeyRequestOptions.allowCredentials !== undefined) {
          publicKeyRequestOptions.allowCredentials = publicKeyRequestOptions.allowCredentials.map(
              data => {
                  const id = base64UrlDecode(data.id);
                  return {
                      type: data.type,
                      id: Uint8Array.from(id, c => c.charCodeAt(0)),
                  };
              }
          );
      }

      navigator.credentials
          .get({publicKey: publicKeyRequestOptions})
          .then(data => {
              function arrayToBase64String(a) {
                  return btoa(String.fromCharCode(...a));
              }

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
                          : null,
                  },
              };
              handlePublicKeyRequestResult(
                  publicKeyCredential,
                  this.loginSuccessHandler,
                  this.loginFailureHandler
              );
          })
          .catch(this.loginFailureHandler);
  };

  loginFailureHandler = error => {
      console.log(error);
      this.props.enqueueSnackbar({
          message:
        'An error occurred during the login process. Please try again later.',
      });
      this.setState({
          isDeviceInteractionEnabled: false,
      });
  };

  loginSuccessHandler = json => {
      console.log(json);
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
          this.loginFailureHandler();
      }
  };

  render() {
      const {classes, ...rest} = this.props;
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

/*

function detectWebAuthnSupport() {
    if (window.PublicKeyCredential === undefined ||
        typeof window.PublicKeyCredential !== "function") {
        $('#register-button').attr("disabled", true);
        $('#login-button').attr("disabled", true);
        var errorMessage = "Oh no! This browser doesn't currently support WebAuthn."
        if (window.location.protocol === "http:" && (window.location.hostname !== "localhost" && window.location.hostname !== "127.0.0.1")){
            errorMessage = "WebAuthn only supports secure connections. For testing over HTTP, you can use the origin \"localhost\"."
        }
        showErrorAlert(errorMessage);
        return;
    }
}

function string2buffer(str) {
    return (new Uint8Array(str.length)).map(function (x, i) {
        return str.charCodeAt(i)
    });
}

// Encode an ArrayBuffer into a base64 string.
function bufferEncode(value) {
    return base64js.fromByteArray(value)
        .replace(/\+/g, "-")
        .replace(/\//g, "_")
        .replace(/=/g, "");
}

// Don't drop any blanks
// decode
function bufferDecode(value) {
    return Uint8Array.from(atob(value), c => c.charCodeAt(0));
}

function buffer2string(buf) {
    let str = "";
    if (!(buf.constructor === Uint8Array)) {
        buf = new Uint8Array(buf);
    }
    buf.map(function (x) {
        return str += String.fromCharCode(x)
    });
    return str;
}

var state = {
    createResponse: null,
    publicKeyCredential: null,
    credential: null,
    user: {
        name: "testuser@example.com",
        displayName: "testuser",
    },
}

function setUser() {
    username = $("#input-email").val();
    state.user.name = username.toLowerCase().replace(/\s/g, '');
    state.user.displayName = username.toLowerCase();
}

function checkUserExists() {
    $.get('/user/' + state.user.name + '/exists', {}, null, 'json')
        .done(function (response) {
            return true;
        }).catch(function () {
            return false;
        });
}

function getCredentials() {
    $.get('/credential/' + state.user.name, {}, null, 'json')
        .done(function (response) {
            console.log(response)
        });
}

function makeCredential() {
    hideErrorAlert();
    console.log("Fetching options for new credential");
    if ($("#input-email").val() === "") {
        showErrorAlert("Please enter a username");
        return;
    }
    setUser();
    var credential = null;

    var attestation_type = $('#select-attestation').find(':selected').val();
    var authenticator_attachment = $('#select-authenticator').find(':selected').val();

    $.get('/makeCredential/' + state.user.name, {
            attType: attestation_type,
            authType: authenticator_attachment
        }, null, 'json')
        .done(function (makeCredentialOptions) {
            makeCredentialOptions.publicKey.challenge = bufferDecode(makeCredentialOptions.publicKey.challenge);
            makeCredentialOptions.publicKey.user.id = bufferDecode(makeCredentialOptions.publicKey.user.id);
            if (makeCredentialOptions.publicKey.excludeCredentials) {
                for (var i = 0; i < makeCredentialOptions.publicKey.excludeCredentials.length; i++) {
                    makeCredentialOptions.publicKey.excludeCredentials[i].id = bufferDecode(makeCredentialOptions.publicKey.excludeCredentials[i].id);
                }
            }
            console.log("Credential Creation Options");
            console.log(makeCredentialOptions);
            navigator.credentials.create({
                publicKey: makeCredentialOptions.publicKey
            }).then(function (newCredential) {
                console.log("PublicKeyCredential Created");
                console.log(newCredential);
                state.createResponse = newCredential;
                registerNewCredential(newCredential);
            }).catch(function (err) {
                console.info(err);
            });
        });
}

// This should be used to verify the auth data with the server
function registerNewCredential(newCredential) {
    // Move data into Arrays incase it is super long
    let attestationObject = new Uint8Array(newCredential.response.attestationObject);
    let clientDataJSON = new Uint8Array(newCredential.response.clientDataJSON);
    let rawId = new Uint8Array(newCredential.rawId);

    $.ajax({
        url: '/makeCredential',
        type: 'POST',
        data: JSON.stringify({
            id: newCredential.id,
            rawId: bufferEncode(rawId),
            type: newCredential.type,
            response: {
                attestationObject: bufferEncode(attestationObject),
                clientDataJSON: bufferEncode(clientDataJSON),
            },
        }),
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function (response) {
            $("#login-button").popover('show')
        }
    });
}

function addUserErrorMsg(msg) {
    if (msg === "username") {
        msg = 'Please add username';
    } else {
        msg = 'Please add email';
    }
    document.getElementById("user-create-error").innerHTML = msg;
}

function getAssertion() {
    hideErrorAlert();
    if ($("#input-email").val() === "") {
        showErrorAlert("Please enter a username");
        return;
    }
    setUser();
    $.get('/user/' + state.user.name + '/exists', {}, null, 'json').done(function (response) {
            console.log(response);
        }).then(function () {
            $.get('/assertion/' + state.user.name, {}, null, 'json')
                .done(function (makeAssertionOptions) {
                    console.log("Assertion Options:");
                    console.log(makeAssertionOptions);
                    makeAssertionOptions.publicKey.challenge = bufferDecode(makeAssertionOptions.publicKey.challenge);
                    makeAssertionOptions.publicKey.allowCredentials.forEach(function (listItem) {
                        listItem.id = bufferDecode(listItem.id)
                    });
                    console.log(makeAssertionOptions);
                    navigator.credentials.get({
                            publicKey: makeAssertionOptions.publicKey
                        })
                        .then(function (credential) {
                            console.log(credential);
                            verifyAssertion(credential);
                        }).catch(function (err) {
                            console.log(err.name);
                            showErrorAlert(err.message);
                        });
                });
        })
        .catch(function (error) {
            if (!error.exists) {
                showErrorAlert("User not found, try registering one first!");
            }
            return;
        });
}

function verifyAssertion(assertedCredential) {
    // Move data into Arrays incase it is super long
    console.log('calling verify')
    let authData = new Uint8Array(assertedCredential.response.authenticatorData);
    let clientDataJSON = new Uint8Array(assertedCredential.response.clientDataJSON);
    let rawId = new Uint8Array(assertedCredential.rawId);
    let sig = new Uint8Array(assertedCredential.response.signature);
    let userHandle = new Uint8Array(assertedCredential.response.userHandle);
    $.ajax({
        url: '/assertion',
        type: 'POST',
        data: JSON.stringify({
            id: assertedCredential.id,
            rawId: bufferEncode(rawId),
            type: assertedCredential.type,
            response: {
                authenticatorData: bufferEncode(authData),
                clientDataJSON: bufferEncode(clientDataJSON),
                signature: bufferEncode(sig),
                userHandle: bufferEncode(userHandle),
            },
        }),
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function (response) {
            window.location = "/dashboard"
            console.log(response)
        }
    });
}

function setCurrentUser(userResponse) {
    state.user.name = userResponse.name;
    state.user.displayName = userResponse.display_name;
}

function showErrorAlert(msg) {
    $("#alert-msg").text(msg)
    $("#alert").show();
}

function hideErrorAlert() {
    $("#alert").hide();
}

function popoverPlacement(context, source) {
    if ($(window).width() < 992) {
        return "bottom"
    }
    return "right";
}

$(document).ready(function () {
    $('[data-toggle="popover"]').popover({
        trigger: 'manual',
        container: 'body',
        placement: popoverPlacement
    })
})
 */

////BASE64
/*
var lookup = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/'

;(function (exports) {
  'use strict'

  var Arr = (typeof Uint8Array !== 'undefined')
    ? Uint8Array
    : Array

  var PLUS = '+'.charCodeAt(0)
  var SLASH = '/'.charCodeAt(0)
  var NUMBER = '0'.charCodeAt(0)
  var LOWER = 'a'.charCodeAt(0)
  var UPPER = 'A'.charCodeAt(0)
  var PLUS_URL_SAFE = '-'.charCodeAt(0)
  var SLASH_URL_SAFE = '_'.charCodeAt(0)

  function decode (elt) {
    var code = elt.charCodeAt(0)
    if (code === PLUS || code === PLUS_URL_SAFE) return 62 // '+'
    if (code === SLASH || code === SLASH_URL_SAFE) return 63 // '/'
    if (code < NUMBER) return -1 // no match
    if (code < NUMBER + 10) return code - NUMBER + 26 + 26
    if (code < UPPER + 26) return code - UPPER
    if (code < LOWER + 26) return code - LOWER + 26
  }

  function b64ToByteArray (b64) {
    var i, j, l, tmp, placeHolders, arr

    if (b64.length % 4 > 0) {
      throw new Error('Invalid string. Length must be a multiple of 4')
    }

    // the number of equal signs (place holders)
    // if there are two placeholders, than the two characters before it
    // represent one byte
    // if there is only one, then the three characters before it represent 2 bytes
    // this is just a cheap hack to not do indexOf twice
    var len = b64.length
    placeHolders = b64.charAt(len - 2) === '=' ? 2 : b64.charAt(len - 1) === '=' ? 1 : 0

    // base64 is 4/3 + up to two characters of the original data
    arr = new Arr(b64.length * 3 / 4 - placeHolders)

    // if there are placeholders, only get up to the last complete 4 chars
    l = placeHolders > 0 ? b64.length - 4 : b64.length

    var L = 0

    function push (v) {
      arr[L++] = v
    }

    for (i = 0, j = 0; i < l; i += 4, j += 3) {
      tmp = (decode(b64.charAt(i)) << 18) | (decode(b64.charAt(i + 1)) << 12) | (decode(b64.charAt(i + 2)) << 6) | decode(b64.charAt(i + 3))
      push((tmp & 0xFF0000) >> 16)
      push((tmp & 0xFF00) >> 8)
      push(tmp & 0xFF)
    }

    if (placeHolders === 2) {
      tmp = (decode(b64.charAt(i)) << 2) | (decode(b64.charAt(i + 1)) >> 4)
      push(tmp & 0xFF)
    } else if (placeHolders === 1) {
      tmp = (decode(b64.charAt(i)) << 10) | (decode(b64.charAt(i + 1)) << 4) | (decode(b64.charAt(i + 2)) >> 2)
      push((tmp >> 8) & 0xFF)
      push(tmp & 0xFF)
    }

    return arr
  }

  function uint8ToBase64 (uint8) {
    var i
    var extraBytes = uint8.length % 3 // if we have 1 byte left, pad 2 bytes
    var output = ''
    var temp, length

    function encode (num) {
      return lookup.charAt(num)
    }

    function tripletToBase64 (num) {
      return encode(num >> 18 & 0x3F) + encode(num >> 12 & 0x3F) + encode(num >> 6 & 0x3F) + encode(num & 0x3F)
    }

    // go through the array every three bytes, we'll deal with trailing stuff later
    for (i = 0, length = uint8.length - extraBytes; i < length; i += 3) {
      temp = (uint8[i] << 16) + (uint8[i + 1] << 8) + (uint8[i + 2])
      output += tripletToBase64(temp)
    }

    // pad the end with zeros, but make sure to not forget the extra bytes
    switch (extraBytes) {
      case 1:
        temp = uint8[uint8.length - 1]
        output += encode(temp >> 2)
        output += encode((temp << 4) & 0x3F)
        output += '=='
        break
      case 2:
        temp = (uint8[uint8.length - 2] << 8) + (uint8[uint8.length - 1])
        output += encode(temp >> 10)
        output += encode((temp >> 4) & 0x3F)
        output += encode((temp << 2) & 0x3F)
        output += '='
        break
      default:
        break
    }

    return output
  }

  exports.toByteArray = b64ToByteArray
  exports.fromByteArray = uint8ToBase64
}(typeof exports === 'undefined' ? (this.base64js = {}) : exports))
*/
