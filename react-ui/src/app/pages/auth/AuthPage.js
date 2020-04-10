import React from "react";
import {Link, Switch, Route, Redirect} from "react-router-dom";
import {toAbsoluteUrl} from "../../../_metronic";
import "../../../_metronic/_assets/sass/pages/login/login-1.scss";
import Login from "./Login";
import Registration from "./Registration";
import ForgotPassword from "./ForgotPassword";
import OktaSignInWidget from '../../widgets/OktaSignInWidget';
import {withAuth} from '@okta/okta-react';


export default withAuth(class AuthPage extends React.Component{
    constructor(props) {
        super(props);
        this.onSuccess = this.onSuccess.bind(this);
        this.onError = this.onError.bind(this);
        this.state = {
            authenticated: null,

        };
        this.checkAuthentication();

    }
    async checkAuthentication() {
        const authenticated = await this.props.auth.isAuthenticated();
        const token = await this.props.auth.getAccessToken();

        if (authenticated !== this.state.authenticated) {
            this.setState({authenticated});
        }

        if (authenticated) {
            localStorage.setItem('accessToken', token);
        }

    }

    async componentDidMount() {
        this.checkAuthentication();
    }

    async componentDidUpdate() {
        this.checkAuthentication();
    }
    onSuccess(res) {
        if (res.status === 'SUCCESS') {
            return this.props.auth.redirect({
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
        if (this.state.authenticated === null) return null;
        return (this.state.authenticated ? <Redirect to={{pathname: '/dashboard'}}/> :
            <>
                <div id="kt_login" className="kt-grid kt-grid--hor kt-grid--root kt-login kt-login--v1">
                    <div
                        className="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--desktop kt-grid--ver-desktop kt-grid--hor-tablet-and-mobile">
                        <div
                            className="kt-grid__item kt-grid__item--order-tablet-and-mobile-2 kt-grid kt-grid--hor kt-login__aside"
                            style={{
                                backgroundImage: `url(${toAbsoluteUrl("/media/bg/bg-4.jpg")})`
                            }}>
                            <div className="kt-grid__item">
                                <Link to="/" className="kt-login__logo">
                                    <img
                                        alt="Logo"
                                        src={toAbsoluteUrl("/media/logos/logo-4.png")}
                                    />
                                </Link>
                            </div>
                            <div className="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver">
                                <div className="kt-grid__item kt-grid__item--middle">
                                    <h3 className="kt-login__title">Welcome to Metronic!</h3>
                                    <h4 className="kt-login__subtitle">
                                        The ultimate Bootstrap & Angular 6 admin theme framework for
                                        next generation web apps.
                                    </h4>
                                </div>
                            </div>
                            <div className="kt-grid__item">
                                <div className="kt-login__info">
                                    <div className="kt-login__copyright">
                                        &copy; 2018 Metronic
                                    </div>
                                    <div className="kt-login__menu">
                                        <Link to="/terms" className="kt-link">
                                            Privacy
                                        </Link>
                                        <Link to="/terms" className="kt-link">
                                            Legal
                                        </Link>
                                        <Link to="/terms" className="kt-link">
                                            Contact
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="kt-grid__item kt-grid__item--fluid  kt-grid__item--order-tablet-and-mobile-1  kt-login__wrapper">
                            <OktaSignInWidget
                                baseUrl={this.props.baseUrl}
                                onSuccess={this.onSuccess}
                                onError={this.onError}/>
                        </div>
                    </div>
                </div>
            </>);
    }
});
