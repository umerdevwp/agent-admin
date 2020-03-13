import React from "react";
import {BrowserRouter as Router, Redirect, Route, Switch, withRouter} from 'react-router-dom';
import {Security, SecureRoute, ImplicitCallback} from '@okta/okta-react';

import {I18nProvider, LayoutSplashScreen, ThemeProvider} from "../../../_metronic";
import HomePage from './HomePage';
import Layout from "../../../_metronic/layout/Layout";
import {LayoutContextProvider} from "../../../_metronic";
import {useLastLocation} from "react-router-last-location";
import * as routerHelpers from "../../router/RouterHelpers";
import {shallowEqual, useSelector} from "react-redux";
import {withAuth} from '@okta/okta-react';


const CustomLayoutForAgentAdmin = withRouter(({history}, props) => {
    const lastLocation = useLastLocation();
    routerHelpers.saveLastLocation(lastLocation);
    const {isAuthorized, menuConfig, userLastLocation} = useSelector(
        ({auth, urls, builder: {menuConfig}}) => ({
            menuConfig,
            isAuthorized: auth.user != null,
            userLastLocation: routerHelpers.getLastLocation()
        }),
        shallowEqual
    );

    return (
        <LayoutContextProvider history={history} menuConfig={menuConfig}>
            <Layout>
                <HomePage props/>
            </Layout>
        </LayoutContextProvider>
    )
});


export default withAuth(CustomLayoutForAgentAdmin);
