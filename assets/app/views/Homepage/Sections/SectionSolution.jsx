import React, { Component } from 'react';
import withStyles from '@material-ui/core/styles/withStyles';

import basicsStyle from 'assets/jss/material-kit-react/views/componentsSections/typographyStyle.jsx';

import GridContainer from 'components/Grid/GridContainer.jsx';
import GridItem from 'components/Grid/GridItem.jsx';

import holdingKey from 'app/img/holding-key.jpg';

class SectionSolution extends Component {
    render() {
        const { classes } = this.props;
        return (
            <div className={ classes.sections }>
                <div className={ classes.container }>
                    <div className={ classes.title }>
                        <h2>Easy + Secure</h2>
                    </div>
                    <div id="solution">
                        <GridContainer justify="center">
                            <GridItem xs={ 6 } sm={ 6 } className={ classes.centered }>
                                <img
                                    src={ holdingKey }
                                    alt="..."
                                    className={ classes.imgRounded + ' ' + classes.imgFluid }
                                />
                                <h3>No password? No troubles.</h3>
                                <p>
                  With Webauthn, you don’t have to care of all of the security
                  problems induced by passwords as you don’t manage them.
                                </p>
                                <p>
                  No sensitive data is stored. You just need to store public
                  keys, counters, certificate chains (optional). You or the
                  users can decide anonymize the data you collect.
                                </p>
                                <p>The data does not pose any risk if leaked.</p>
                            </GridItem>
                        </GridContainer>
                        <GridContainer />
                    </div>
                </div>
                <div className={ classes.space50 } />
            </div>
        );
    }
}

export default withStyles( basicsStyle )( SectionSolution );
