import {Component} from 'react';
import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';
import {logout} from 'app/store/actions/authenticationActions';

class AuthenticationChecker extends Component {
  checkAuthenticationStatus = () => {
      const data = sessionStorage.getItem('authentication_data');
      if (data) {
          fetch('/api/profile', {
              method: 'GET',
              credentials: 'same-origin',
              headers: {
                  'Content-Type': 'application/json',
              },
          })
              .then(response => {
                  return response.json();
              })
              .then(json => {
                  if (json.status === 'error') {
                      this.props.logout();
                  }
              })
              .catch(err => {
                  this.props.logout();
              });
          return;
      }

      const {isAuthenticated} = this.props;
      if (isAuthenticated) {
          this.props.logout();
      }
  };

  componentDidMount = () => {
      setInterval(this.checkAuthenticationStatus, 5000);
  };

  render() {
      return null;
  }
}

function mapStateToProps(state) {
    const {auth} = state;
    let isAuthenticated = false;
    if (auth !== undefined && auth.data !== undefined && auth.data !== null) {
        isAuthenticated = true;
    }
    return {isAuthenticated};
}

const mapDispatchToProps = dispatch => {
    return bindActionCreators({logout}, dispatch);
};

export default connect(
    mapStateToProps,
    mapDispatchToProps
)(AuthenticationChecker);
