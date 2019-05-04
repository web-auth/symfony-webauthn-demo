import React, { Component } from "react";
import { connect } from 'react-redux'
// nodejs library that concatenates classes
import classNames from "classnames";
// @material-ui/core components
import withStyles from "@material-ui/core/styles/withStyles";
// @material-ui/icons
import AccountCircle from "@material-ui/icons/AccountCircle";
import Security from "@material-ui/icons/Security";
import Fingerprint from "@material-ui/icons/Fingerprint";
// core components
import Header from "app/components/Header/Header.jsx";
import Footer from "app/components/Footer/Footer.jsx";
import GridContainer from "components/Grid/GridContainer.jsx";
import GridItem from "components/Grid/GridItem.jsx";
import HeaderLinks from "app/components/Header/HeaderLinks.jsx";
import NavPills from "components/NavPills/NavPills.jsx";
import Parallax from "components/Parallax/Parallax.jsx";

import profile from "app/img/avatar.jpg";

import profilePageStyle from "assets/jss/material-kit-react/views/profilePage.jsx";

class ProfilePage extends Component {
  render() {
    const { classes, authenticationData, ...rest } = this.props;
    const imageClasses = classNames(
      classes.imgRaised,
      classes.imgRoundedCircle,
      classes.imgFluid
    );

    console.log(authenticationData)
    return (
      <div>
        <Header
          color="transparent"
          brand="Webauthn Demo"
          rightLinks={<HeaderLinks />}
          fixed
          changeColorOnScroll={{
            height: 200,
            color: "white"
          }}
          {...rest}
        />
        <Parallax small filter image={require("assets/img/profile-bg.jpg")} />
        <div className={classNames(classes.main, classes.mainRaised)}>
          <div>
            <div className={classes.container}>
              <GridContainer justify="center">
                <GridItem xs={12} sm={12} md={6}>
                  <div className={classes.profile}>
                    <div>
                      <img src={profile} alt="Avatar" className={imageClasses} />
                    </div>
                    <div className={classes.name}>
                      <h3 className={classes.title}>{authenticationData.userEntity.displayName}</h3>
                    </div>
                  </div>
                </GridItem>
              </GridContainer>
              <div className={classes.description}>
                <p>
                    This is your profile page. You will find all the data collected by this demo.<br/>
                    You can also list, add or remove security devices.
                </p>
              </div>
              <GridContainer justify="center">
                <GridItem xs={12} sm={12} md={8} className={classes.navWrapper}>
                  <NavPills
                    alignCenter
                    color="primary"
                    tabs={[
                      {
                        tabButton: "Profile",
                        tabIcon: AccountCircle,
                        tabContent: (
                          <GridContainer justify="center">
                          </GridContainer>
                        )
                      },
                      {
                        tabButton: "Session",
                        tabIcon: Security,
                        tabContent: (
                          <GridContainer justify="center">
                          </GridContainer>
                        )
                      },
                      {
                        tabButton: "Credentials",
                        tabIcon: Fingerprint,
                        tabContent: (
                          <GridContainer justify="center">
                          </GridContainer>
                        )
                      }
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
    const { auth } = state
    return { authenticationData: auth.data }
}

export default connect(mapStateToProps)(withStyles(profilePageStyle)(ProfilePage));
