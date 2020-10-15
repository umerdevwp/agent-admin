import React, {Component} from 'react';
import {Redirect} from 'react-router-dom';
import OktaSignInWidget from './OktaSignInWidget';
import {withOktaAuth} from '@okta/okta-react';
import CssBaseline from '@material-ui/core/CssBaseline';
import Link from '@material-ui/core/Link';
import Box from '@material-ui/core/Box';
import Typography from '@material-ui/core/Typography';
import {makeStyles} from '@material-ui/core/styles';
import Container from '@material-ui/core/Container';
import {withStyles} from '@material-ui/styles';
import PropTypes from 'prop-types';
import Particles from 'react-particles-js';

function Copyright() {
    return (
        <Typography className={'copyright'} variant="body2" color="textSecondary" align="center">
            {'Copyright © '}
            <Link color="inherit" href="/">
                AgentAdmin
            </Link>{' '}
            {new Date().getFullYear()}
            {'.'}
        </Typography>
    );
}

const useStyles = makeStyles((theme) => ({

    paperContainer: {
        backgroundColor: '#000000'
    },

    paper: {
        marginTop: theme.spacing(8),
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
    },
    avatar: {
        margin: theme.spacing(1),
        backgroundColor: theme.palette.secondary.main,
    },
    form: {
        width: '100%', // Fix IE 11 issue.
        marginTop: theme.spacing(1),
    },
    submit: {
        margin: theme.spacing(3, 0, 2),
    },
}));


class Login extends Component {
    constructor(props) {
        super(props);
        this.onSuccess = this.onSuccess.bind(this);
        this.onError = this.onError.bind(this);
    }

    onSuccess(res) {
        if (res.status === 'SUCCESS') {
            return this.props.authService.redirect({
                sessionToken: res.session.token
            });
        } else {
            // The user can be in another authentication state that requires further action.
            // For more information about these states, see:
            //   https://github.com/okta/okta-signin-widget#rendereloptions-success-error
        }
    }

    onError(err) {
        console.log('error logging in', err);
    }

    render() {

        const {classes} = this.props;
        if (this.props.authState.isPending && this.props.authState.isAuthenticated) return null;
        return this.props.authState.isAuthenticated ?
            <Redirect to={{pathname: '/'}}/> :

            <div className={'paperContainer'}>
                <Particles   style={{
                    width: '100%',
                    position:'absolute'
                }} className={'particles'} />
                <Container component="main" maxWidth="xs">
                    <CssBaseline/>
                    <div className={classes.paper}>
                        <Box className={'okta-custom'} mt={3}>
                            <OktaSignInWidget
                                baseUrl={this.props.baseUrl}
                                onSuccess={this.onSuccess}
                                onError={this.onError}/>
                        </Box>
                    </div>
                    <Box>
                        <Copyright/>
                    </Box>
                </Container>

            </div>

    }
};

Login.propTypes = {
    classes: PropTypes.object.isRequired,
};

export default withOktaAuth(Login);



