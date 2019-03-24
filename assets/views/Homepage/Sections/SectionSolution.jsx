import React, { Component } from "react";
import withStyles from "@material-ui/core/styles/withStyles";

import basicsStyle from "assets/jss/material-kit-react/views/componentsSections/typographyStyle.jsx";

import GridContainer from "components/Grid/GridContainer.jsx";
import GridItem from "components/Grid/GridItem.jsx";

import holdingKey from "assets/img/holding-key.jpg";
import SectionDownload from '../Homepage';

class SectionSolution extends Component {
  render() {
    const { classes } = this.props;
    return (
      <div className={classes.sections}>
        <div className={classes.container}>
          <div className={classes.title}>
            <h2>Easy + Secure</h2>
          </div>
          <div id="solution">
            <GridContainer>
              <GridItem xs={6} sm={6} className={classes.centered}>
                <img
                  src={holdingKey}
                  alt="..."
                  className={classes.imgRounded + " " + classes.imgFluid}
                />
                <h3>No password? No troubles.</h3>
                <p>
                  With Webauthn, you don’t have to care of all of the security
                  problems induced by passwords as you don’t manage them. You
                  just need to store public key that does not pose any risk when
                  leaked.
                </p>
              </GridItem>
            </GridContainer>
            <GridContainer />
          </div>
        </div>
        <div className={classes.space50} />
      </div>
    );
  }
}

export default withStyles(basicsStyle)(SectionSolution);
