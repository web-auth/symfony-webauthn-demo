import React, { Component } from "react";

// material-ui components
import withStyles from "@material-ui/core/styles/withStyles";
import Slide from "@material-ui/core/Slide";
import Dialog from "@material-ui/core/Dialog";
import DialogTitle from "@material-ui/core/DialogTitle";
import DialogContent from "@material-ui/core/DialogContent";
import IconButton from "@material-ui/core/IconButton";
// @material-ui/icons
import Close from "@material-ui/icons/Close";

import modalStyle from "assets/jss/material-kit-react/modalStyle.jsx";

function Transition(props) {
  return <Slide direction="up" {...props} />;
}

class PublicKeyCreationError extends Component {
  render() {
    const { classes } = this.props;
    return (
      <Dialog
        classes={{
          root: classes.center,
          paper: classes.modal
        }}
        open={this.props.isOpened}
        TransitionComponent={Transition}
        keepMounted
        onClose={() => this.props.handleClose()}
        aria-labelledby="modal-slide-title"
        aria-describedby="modal-slide-description"
      >
        <DialogTitle
          id="classic-modal-slide-title"
          disableTypography
          className={classes.modalHeader}
        >
          <IconButton
            className={classes.modalCloseButton}
            key="close"
            aria-label="Close"
            color="inherit"
            onClick={() => this.props.handleClose()}
          >
            <Close className={classes.modalClose} />
          </IconButton>
          <h4 className={classes.modalTitle}>An error occurred</h4>
        </DialogTitle>
        <DialogContent
          id="modal-slide-description"
          className={classes.modalBody}
        >
          <h5>
            We are sorry but the registration process failed. Please try again
            later. The administrators have been notified.
          </h5>
        </DialogContent>
      </Dialog>
    );
  }
}

export default withStyles(modalStyle)(PublicKeyCreationError);
