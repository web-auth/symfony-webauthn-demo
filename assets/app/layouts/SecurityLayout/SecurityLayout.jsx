import React, {Component} from 'react';
// @material-ui/core components
import withStyles from '@material-ui/core/styles/withStyles';

// core components
import Header from 'app/components/Header/Header.jsx';
import HeaderLinks from 'app/components/Header/HeaderLinks.jsx';
import Footer from 'app/components/Footer/Footer.jsx';
import GridContainer from 'components/Grid/GridContainer.jsx';
import GridItem from 'components/Grid/GridItem.jsx';
import Card from 'components/Card/Card.jsx';

import loginPageStyle from 'assets/jss/material-kit-react/views/loginPage.jsx';

import image from 'assets/img/bg7.jpg';

class SecurityLayout extends Component {
  state = {
      cardAnimation: 'cardHidden',
  };

  cardAnimation = () => {
      this.setState({cardAnimation: ''});
  };

  componentDidMount = () => {
      setTimeout(this.cardAnimation, 700);
  };

  render() {
      const {classes, ...rest} = this.props;

      return (
          <div>
              <Header absolute color="transparent" brand="Webauthn Demo" rightLinks={<HeaderLinks />}{...rest}/>
              <div className={classes.pageHeader} style={{
                  backgroundImage: 'url(' + image + ')',
                  backgroundSize: 'cover',
                  backgroundPosition: 'top center',
              }}>
                  <div className={classes.container}>
                      <GridContainer justify="center">
                          <GridItem xs={12} sm={12} md={4}>
                              <Card className={classes[this.state.cardAnimation]}>
                                  { this.props.children }
                              </Card>
                          </GridItem>
                      </GridContainer>
                  </div>
                  <Footer whiteFont />
              </div>
          </div>
      );
  }
}

export default withStyles(loginPageStyle)(SecurityLayout);
