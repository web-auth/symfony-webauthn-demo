/*eslint-disable*/
import React from "react";

import {Link} from "react-router-dom";

// @material-ui/core components
import withStyles from "@material-ui/core/styles/withStyles";
import List from "@material-ui/core/List";
import ListItem from "@material-ui/core/ListItem";
import Tooltip from "@material-ui/core/Tooltip";

// @material-ui/icons
import { Launch, AccountCircle, PersonAdd, ExitToApp, Home } from "@material-ui/icons";

// core components
import CustomDropdown from "components/CustomDropdown/CustomDropdown.jsx";
import Button from "components/CustomButtons/Button.jsx";

import headerLinksStyle from "assets/jss/material-kit-react/components/headerLinksStyle.jsx";

function HeaderLinks({ ...props }) {
  const { classes } = props;
  return (
    <List className={classes.list}>
      <ListItem className={classes.listItem}>
        <Button
          to="/"
          color="transparent"
          component={Link}
          className={classes.navLink}
        >
          <Home className={classes.icons} /> Homepage
        </Button>
      </ListItem>
      <ListItem className={classes.listItem}>
        <Button
          to="/login"
          color="transparent"
          component={Link}
          className={classes.navLink}
        >
          <AccountCircle className={classes.icons} /> Sign in
        </Button>
      </ListItem>
      <ListItem className={classes.listItem}>
        <Button
          to="/register"
          color="transparent"
          component={Link}
          className={classes.navLink}
        >
          <PersonAdd className={classes.icons} /> Sign up
        </Button>
      </ListItem>
      <ListItem className={classes.listItem}>
        <Button
          href="https://github.com/web-auth"
          color="transparent"
          target="_blank"
          rel="noopener noreferrer"
          className={classes.navLink}
        >
          <Launch className={classes.icons} /> Download
        </Button>
      </ListItem>
    </List>
  );
}

export default withStyles(headerLinksStyle)(HeaderLinks);
