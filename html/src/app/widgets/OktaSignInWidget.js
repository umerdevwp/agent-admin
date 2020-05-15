import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import OktaSignIn from '@okta/okta-signin-widget';
import '@okta/okta-signin-widget/dist/css/okta-sign-in.min.css';

export default class OktaSignInWidget extends Component {
    componentDidMount() {
        const el = ReactDOM.findDOMNode(this);
        this.widget = new OktaSignIn({
            baseUrl: process.env.REACT_APP_OKTA_BASE_URL,
            features: {
                idpDiscovery: true,
                showPasswordToggleOnSignInPage: true
            },
            idps: [
                {type: process.env.REACT_APP_OKTA_IDPS_TYPE, id: process.env.REACT_APP_OKTA_IDPS_ID},
            ],
            clientId: process.env.REACT_APP_OKTA_CLIENT_ID,
            redirectUri: process.env.REACT_APP_OKTA_REDIRECT_URI,
            authScheme: 'OAUTH2',
            authParams: {
                issuer: process.env.REACT_APP_OKTA_BASE_URL + 'oauth2/default',
                display: 'page',
                responseType: ['id_token', 'token'],
                scopes: ['openid', 'email', 'profile', 'address', 'phone'],
                authorizeUrl: process.env.REACT_APP_OKTA_BASE_URL + 'oauth2/default/v1/authorize',
            },
        });
        this.widget.renderEl({el}, this.props.onSuccess, this.props.onError);
    }

    componentWillUnmount() {
        this.widget.remove();
    }

    render() {
        return <div/>;
    }
};
