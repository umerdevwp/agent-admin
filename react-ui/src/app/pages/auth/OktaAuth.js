import React, {Component} from 'react';
import {Link, Redirect} from 'react-router-dom';
import OktaSignInWidget from '../../widgets/OktaSignInWidget';
import {withAuth} from '@okta/okta-react';
import {toAbsoluteUrl} from "../../../_metronic";
import "../../../_metronic/_assets/sass/pages/login/login-1.scss";
import {Formik} from "formik";
import {connect} from "react-redux";
import {FormattedMessage, injectIntl} from "react-intl";
import {TextField} from "@material-ui/core";
import clsx from "clsx";

export default withAuth(class OktaAuth extends Component {

    constructor(props) {
        super(props);
        this.onSuccess = this.onSuccess.bind(this);
        this.onError = this.onError.bind(this);
        this.state = {
            authenticated: null,

        };
        this.checkAuthentication();

    }
    static contextType = OktaUserContext

    async checkAuthentication() {
        const checkifAdmin = this.context
        const authenticated = await this.props.auth.isAuthenticated();
        const token = await this.props.auth.getAccessToken();

        if (authenticated !== this.state.authenticated) {
            this.setState({authenticated});

        }

    }

    componentDidUpdate() {
        this.checkAuthentication();
    }

    onSuccess(res) {
        if (res.status === 'SUCCESS') {
        } else {
        }
    }

    onError(err) {
        console.log('error logging in', err);
    }

    render() {
        if (this.state.authenticated === null) return null;
        return this.state.authenticated ?
            <Redirect to={{pathname: '/dashboard'}}/> :

            <div className="kt-grid kt-grid--ver kt-grid--root">
                <div id="kt_login"
                     className="kt-grid kt-grid--hor kt-grid--root kt-login kt-login--v1">
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

                        <div
                            className="kt-grid__item kt-grid__item--fluid  kt-grid__item--order-tablet-and-mobile-1  kt-login__wrapper">
                            {/*<OktaSignInWidget*/}
                            {/*    baseUrl={this.props.baseUrl}*/}
                            {/*    onSuccess={this.onSuccess}*/}
                            {/*    onError={this.onError}/>*/}
                        </div>
                    </div>
                </div>
            </div>
    }
});

