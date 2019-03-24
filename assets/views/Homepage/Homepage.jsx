import React, { Component } from "react";
import classNames from "classnames";
import { Link } from "react-router-dom";
import withStyles from "@material-ui/core/styles/withStyles";
import Header from "components/Header/Header.jsx";
import Footer from "components/Footer/Footer.jsx";
import GridContainer from "components/Grid/GridContainer.jsx";
import GridItem from "components/Grid/GridItem.jsx";
import Parallax from "components/Parallax/Parallax.jsx";
import HeaderLinks from "components/Header/HeaderLinks.jsx";

import SectionTroubles from "./Sections/SectionTroubles";
import SectionSolution from "./Sections/SectionSolution";
import SectionDownload from "./Sections/SectionDownload";

import componentsStyle from "assets/jss/material-kit-react/views/components.jsx";

class Homepage extends Component {
  render() {
    const { classes, ...rest } = this.props;
    return (
      <div>
        <Header
          brand="Webauthn Demo"
          rightLinks={<HeaderLinks />}
          fixed
          color="transparent"
          changeColorOnScroll={{
            height: 400,
            color: "white"
          }}
          {...rest}
        />
        <Parallax image={require("assets/img/header.jpg")}>
          <div className={classes.container}>
            <GridContainer>
              <GridItem>
                <div className={classes.brand}>
                  <h1 className={classes.title}>WEBAUTHN + SYMFONY =💖</h1>
                  <h3 className={classes.subtitle}>
                    Get rid of the user passwords and quickly build an
                    application supporting Webauthn without any effort.
                  </h3>
                </div>
              </GridItem>
            </GridContainer>
          </div>
        </Parallax>

        <div className={classNames(classes.main, classes.mainRaised)}>
          <SectionTroubles />
          <SectionSolution />
          <SectionDownload />
        </div>
        <Footer />
      </div>
    );
  }
}

export default withStyles(componentsStyle)(Homepage);
