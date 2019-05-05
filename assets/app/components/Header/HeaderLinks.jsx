import React from 'react';
import { connect } from 'react-redux';

import { Link } from 'react-router-dom';

// @material-ui/core components
import withStyles from '@material-ui/core/styles/withStyles';
import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';

// @material-ui/icons
import {
    Launch,
    AccountCircle,
    PersonAdd,
    Home,
    Apps,
} from '@material-ui/icons';

// core components
import CustomDropdown from 'components/CustomDropdown/CustomDropdown.jsx';
import Button from 'components/CustomButtons/Button.jsx';

import headerLinksStyle from 'assets/jss/material-kit-react/components/headerLinksStyle.jsx';

function HeaderLinks( { ...props } ) {
    const { classes, isAuthenticated } = props;

    if ( isAuthenticated ) {
        return (
            <List className={ classes.list }>
                <ListItem className={ classes.listItem }>
                    <Button
                        to="/"
                        color="transparent"
                        component={ Link }
                        className={ classes.navLink }
                    >
                        <Home className={ classes.icons } /> Homepage
                    </Button>
                </ListItem>
                <ListItem className={ classes.listItem }>
                    <CustomDropdown
                        noLiPadding
                        buttonText="Profile"
                        buttonProps={ {
                            className: classes.navLink,
                            color: 'transparent',
                        } }
                        buttonIcon={ AccountCircle }
                        dropdownList={ [
                            <Link to="/profile" className={ classes.dropdownLink }>
                Show profile
                            </Link>,
                            <Link to="/logout" className={ classes.dropdownLink }>
                Logout
                            </Link>,
                        ] }
                    />
                </ListItem>
                <ListItem className={ classes.listItem }>
                    <Button
                        href="https://github.com/web-auth"
                        color="transparent"
                        target="_blank"
                        rel="noopener noreferrer"
                        className={ classes.navLink }
                    >
                        <Launch className={ classes.icons } /> Download
                    </Button>
                </ListItem>
            </List>
        );
    } else {
        return (
            <List className={ classes.list }>
                <ListItem className={ classes.listItem }>
                    <Button
                        to="/"
                        color="transparent"
                        component={ Link }
                        className={ classes.navLink }
                    >
                        <Home className={ classes.icons } /> Homepage
                    </Button>
                </ListItem>
                <ListItem className={ classes.listItem }>
                    <Button
                        to="/login"
                        color="transparent"
                        component={ Link }
                        className={ classes.navLink }
                    >
                        <AccountCircle className={ classes.icons } /> Sign in
                    </Button>
                </ListItem>
                <ListItem className={ classes.listItem }>
                    <Button
                        to="/register"
                        color="transparent"
                        component={ Link }
                        className={ classes.navLink }
                    >
                        <PersonAdd className={ classes.icons } /> Sign up
                    </Button>
                </ListItem>
                <ListItem className={ classes.listItem }>
                    <Button
                        href="https://github.com/web-auth"
                        color="transparent"
                        target="_blank"
                        rel="noopener noreferrer"
                        className={ classes.navLink }
                    >
                        <Launch className={ classes.icons } /> Download
                    </Button>
                </ListItem>
            </List>
        );
    }
}

function mapStateToProps( state ) {
    const { auth } = state;
    let isAuthenticated = false;
    if ( auth !== undefined && auth.data !== undefined && auth.data !== null ) {
        isAuthenticated = true;
    }
    return { isAuthenticated };
}

export default connect( mapStateToProps )(
    withStyles( headerLinksStyle )( HeaderLinks )
);
