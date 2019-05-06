import React, {Component, Fragment} from 'react';
import {connect} from 'react-redux';
// nodejs library that concatenates classes
import classNames from 'classnames';
// @material-ui/core components
import withStyles from '@material-ui/core/styles/withStyles';
// @material-ui/icons
import AccountCircle from '@material-ui/icons/AccountCircle';
import Security from '@material-ui/icons/Security';
import Fingerprint from '@material-ui/icons/Fingerprint';
// core components
import Header from 'app/components/Header/Header.jsx';
import Footer from 'app/components/Footer/Footer.jsx';
import GridContainer from 'components/Grid/GridContainer.jsx';
import GridItem from 'components/Grid/GridItem.jsx';
import HeaderLinks from 'app/components/Header/HeaderLinks.jsx';
import NavPills from 'components/NavPills/NavPills.jsx';
import Parallax from 'components/Parallax/Parallax.jsx';

import profile from 'app/img/avatar.jpg';

import profilePageStyle from 'assets/jss/material-kit-react/views/profilePage.jsx';

class ProfilePage extends Component {
    state = {
        profile: null,
    };
    componentDidMount = () => {
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
                if (json.status !== 'error') {
                    this.setState({profile: json})
                }
            });
    }
    render() {
        const {classes, authenticationData, ...rest} = this.props;
        const imageClasses = classNames(
            classes.imgRaised,
            classes.imgRoundedCircle,
            classes.imgFluid
        );

        let credentials = 'No credentials have been registered.'
        if (this.state.profile !== null) {
            credentials = this.state.profile.credentials.map((item, id) => (
                <Fragment key={item.publicKeyCredentialId}>
                    <dt>ID: <pre>{ item.publicKeyCredentialId }</pre></dt>
                    <dd>
                        <ul>
                            <li>AAGUID: { item.aaguid }</li>
                            <li>Public Key: { item.credentialPublicKey }</li>
                            <li>Counter: { item.counter }</li>
                            <li>Attestation type: { item.attestationType }</li>
                            <li>Trust path: { item.trustPath.type }</li>
                        </ul>
                    </dd>
                </Fragment>
            ))
        }

        return (
            <div>
                <Header
                    color="transparent"
                    brand="Webauthn Demo"
                    rightLinks={<HeaderLinks />}
                    fixed
                    changeColorOnScroll={{
                        height: 200,
                        color: 'white',
                    }}
                    {...rest}
                />
                <Parallax small filter image={require('assets/img/profile-bg.jpg')} />
                <div className={classNames(classes.main, classes.mainRaised)}>
                    <div>
                        <div className={classes.container}>
                            <GridContainer justify="center">
                                <GridItem xs={12} sm={12} md={6}>
                                    <div className={classes.profile}>
                                        <div>
                                            <img
                                                src={profile}
                                                alt="Avatar"
                                                className={imageClasses}
                                            />
                                        </div>
                                        <div className={classes.name}>
                                            <h3 className={classes.title}>
                                                { authenticationData.userEntity.displayName }
                                            </h3>
                                        </div>
                                    </div>
                                </GridItem>
                            </GridContainer>
                            <div className={classes.description}>
                                <p>
                  This is your profile page. You will find all the data
                  collected by this demo.
                                    <br />
                  You can also list, add or remove security devices.
                                </p>
                            </div>
                            <GridContainer justify="center">
                                <GridItem xs={12} sm={12} md={8} className={null}>
                                    <NavPills
                                        alignCenter
                                        color="primary"
                                        tabs={[
                                            {
                                                tabButton: 'Profile',
                                                tabIcon: AccountCircle,
                                                tabContent: <GridContainer justify="center">
                                                    <GridItem>
                                                        <h3>Profile details</h3>
                                                        <ul>
                                                            <li>Username: { authenticationData.userEntity.name }</li>
                                                            <li>Creation date: { this.state.profile ? this.state.profile.created_at : '…' }</li>
                                                            <li>Last login at: { this.state.profile ? this.state.profile.last_login_at : '…' }</li>
                                                        </ul>
                                                    </GridItem>
                                                </GridContainer>,
                                            },
                                            {
                                                tabButton: 'Session',
                                                tabIcon: Security,
                                                tabContent: <GridContainer justify="center">
                                                    <GridItem>
                                                        <h3>Session details</h3>
                                                        <ul>
                                                            <li>Authenticator used: <pre>{ authenticationData.credentialDescriptor.id }</pre></li>
                                                            <li>User was present: { authenticationData.isUserPresent ? 'yes' : 'no' }</li>
                                                            <li>User was verified: { authenticationData.isUserVerified ? 'yes' : 'no' }</li>
                                                        </ul>
                                                    </GridItem>
                                                </GridContainer>,
                                            },
                                            {
                                                tabButton: 'Credentials',
                                                tabIcon: Fingerprint,
                                                tabContent: <GridContainer justify="center">
                                                    <GridItem>
                                                        <h3>Registered credentials</h3>

                                                        <dl>
                                                            { credentials }
                                                        </dl>
                                                    </GridItem>
                                                </GridContainer>,
                                            },
                                        ]}
                                    />
                                </GridItem>
                            </GridContainer>
                        </div>
                    </div>
                </div>
                <Footer />
            </div>
        );
    }
}

function mapStateToProps(state) {
    const {auth} = state;
    return {authenticationData: auth.data};
}

export default connect(mapStateToProps)(
    withStyles(profilePageStyle)(ProfilePage)
);
