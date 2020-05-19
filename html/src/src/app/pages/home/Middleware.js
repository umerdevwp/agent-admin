import React, {Suspense, lazy, useContext, useEffect, useState} from "react";
import Builder from "./Builder";
import Dashboard from "./Dashboard";
import EntityDetailedPage from '../entity/EntityDetailedPage';
import DocsPage from "./docs/DocsPage";
import {LayoutSplashScreen} from "../../../_metronic";
import AddEntityForm from '../entity/AddEntityForm';
import Test from '../entity/TestAutocomplete';
import Admins from '../admins/Admins';
import Contacts from '../contacts/Contacts';
import Attachments from '../attachments/Attachments';
import AddContactForm from '../contacts/AddContactForm';
import AddAttachmentForm from '../attachments/AddAttachmentForm';
import RegisteredAgents from '../ra/RegisteredAgents';

import {BrowserRouter as Router, Redirect, Route, Switch, withRouter} from 'react-router-dom';
import {Security, SecureRoute, ImplicitCallback} from '@okta/okta-react';
import {withAuth} from '@okta/okta-react';
import {UserContext} from '../../context/UserContext';
import {OktaUserContext} from "../../context/OktaUserContext";
import {fetchUserProfile} from '../../crud/auth.crud';
import {checkAdmin} from '../../crud/enitity.crud';

function Middleware() {
    const splashScreen = document.getElementById("splash-screen");
    splashScreen.classList.remove("hidden");
    // const [dataFetch, setDataFetch] = useState(false);
    // const splashScreen = document.getElementById("splash-screen");
    //
    // const {loading} = useContext(OktaUserContext);

    splashScreen.classList.add("hidden");


    // return <Redirect to={{pathname: '/dashboard'}}/>;


}

export default (Middleware);
