import React from "react";
import {Route, Switch, withRouter} from 'react-router-dom';
import {Security, SecureRoute, ImplicitCallback} from '@okta/okta-react';
import {I18nProvider, LayoutContextProvider, LayoutSplashScreen, ThemeProvider} from "../../_metronic";
import AuthPage from '../pages/auth/AuthPage';
import * as routerHelpers from "../router/RouterHelpers";
import {shallowEqual, useSelector} from "react-redux";
import CustomLayoutForAgentAdmin from '../pages/home/CustomLayoutForAgentAdmin';
import EntityDetailedPage from '../pages/entity/EntityDetailedPage';
import UserContextProvider from '../context/UserContext';
import ErrorsPage from "../pages/errors/ErrorsPage";
import OktaUserContextProvider from '../context/OktaUserContext';
// import Middleware from '../pages/home/Middleware';
import Privacy from '../pages/privacy/privacy';

function onAuthRequired({history}) {
    history.push('/');
}

const AgentAdminRoutes = withRouter(({history}) => {
    return (
        <>



    <Security className={'kt-grid kt-grid--ver kt-grid--root'}
              issuer={process.env.REACT_APP_OKTA_BASE_URL + 'oauth2/default'}
              clientId={process.env.REACT_APP_OKTA_CLIENT_ID}
              redirectUri={window.location.origin + '/implicit/callback'}
              onAuthRequired={onAuthRequired}
              pkce={true}>

        <Route exact path='/' exact={true} component={AuthPage}/>
        <Route exact path='/privacy-policy' component={Privacy}/>

        <OktaUserContextProvider>
            <SecureRoute exact path='/dashboard' component={CustomLayoutForAgentAdmin}/>
            <Route exact path='/dashboard/entity/:id' component={CustomLayoutForAgentAdmin}/>
            <Route exact={true} path='/dashboard/entity/form/add' component={CustomLayoutForAgentAdmin}/>
            <Route exact={true} path='/dashboard/admins' component={CustomLayoutForAgentAdmin}/>
            <Route exact={true} path='/dashboard/contacts' component={CustomLayoutForAgentAdmin}/>
            <Route exact={true} path='/dashboard/contact/form/add/:id' component={CustomLayoutForAgentAdmin}/>
            <Route exact={true} path='/dashboard/attachments' component={CustomLayoutForAgentAdmin}/>
            <Route exact={true} path='/dashboard/attachment/form/add/:id' component={CustomLayoutForAgentAdmin}/>
            <Route exact={true} path='/dashboard/agents' component={CustomLayoutForAgentAdmin}/>
            <Route exact={true} path='/test' component={CustomLayoutForAgentAdmin}/>
        </OktaUserContextProvider>
        <Route path='/implicit/callback' component={ImplicitCallback}/>
        <Route path="/error" component={ErrorsPage}/>

    </Security>
</>
)
});


export default AgentAdminRoutes;
