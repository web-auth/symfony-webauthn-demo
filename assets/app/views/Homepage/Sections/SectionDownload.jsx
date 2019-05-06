import React, {Component} from 'react';
import withStyles from '@material-ui/core/styles/withStyles';

import GridContainer from 'components/Grid/GridContainer.jsx';
import GridItem from 'components/Grid/GridItem.jsx';

import Button from 'components/CustomButtons/Button.jsx';
import basicsStyle from 'assets/jss/material-kit-react/views/componentsSections/typographyStyle.jsx';

class SectionDownload extends Component {
    render() {
        const {classes} = this.props;
        return (
            <div className={classes.sections}>
                <div className={classes.container}>
                    <div className={classes.title}>
                        <h2>Free Download on Github!</h2>
                    </div>
                    <div id="download">
                        <div className={classes.space50} />
                        <GridContainer justify="center">
                            <GridItem xs={6} sm={6} className={classes.centered}>
                                <Button
                                    fullWidth={true}
                                    target="_blank"
                                    href="https://github.com/web-auth"
                                    rel="noopener noreferrer"
                                    color="primary"
                                >
                  Download now!
                                </Button>
                            </GridItem>
                        </GridContainer>
                    </div>
                </div>
                <div className={classes.space50} />
            </div>
        );
    }
}

export default withStyles(basicsStyle)(SectionDownload);
