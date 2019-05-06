import React, {Component} from 'react';
import withStyles from '@material-ui/core/styles/withStyles';

import GridContainer from 'components/Grid/GridContainer.jsx';
import GridItem from 'components/Grid/GridItem.jsx';

import basicsStyle from 'assets/jss/material-kit-react/views/componentsSections/basicsStyle.jsx';

class SectionTroubles extends Component {
    render() {
        const {classes} = this.props;
        return (
            <div className={classes.sections}>
                <div className={classes.container}>
                    <div className={classes.title}>
                        <h2>Have you troubles with password?</h2>
                    </div>
                    <div id="troubles">
                        <GridContainer justify="center">
                            <GridItem xs={6} sm={6} className={classes.centered}>
                                <ul>
                                    <li>Do you remember it?</li>
                                    <li>Is your password secured enough?</li>
                                    <li>
                    Does it include numbers, mixes caps and special chars?
                                    </li>
                                    <li>When did you changed it? How old is it?</li>
                                    <li>Is it the same as the one you used few months ago?</li>
                                    <li>
                    Have you been{ ' ' }
                                        <a
                                            href="https://haveibeenpwned.com/"
                                            rel="noopener noreferrer"
                                            target="_blank"
                                        >
                      PAWNED
                                        </a>{ ' ' }
                    ?
                                    </li>
                                </ul>
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

export default withStyles(basicsStyle)(SectionTroubles);
