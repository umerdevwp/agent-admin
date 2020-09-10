import React, {Suspense, lazy, useContext, useEffect, useState} from "react";
import Builder from "./Builder";
import Dashboard from "./Dashboard";
import EntityDetailedPage from '../entity/EntityDetailedPage';
import SelfDetailedPage from '../entity/SelfDetailedPage';
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
import DatatableListing from '../entity/DatatableListing';
import {BrowserRouter as Router, Redirect, Route, Switch, withRouter} from 'react-router-dom';
import {Security, SecureRoute, ImplicitCallback} from '@okta/okta-react';
import {withAuth} from '@okta/okta-react';
import {UserContext} from '../../context/UserContext';
import {fetchUserProfile} from '../../crud/auth.crud';
import {checkAdmin} from '../../crud/enitity.crud';

function HomePage(props) {

    // const {setprofileforUser} = useContext(UserContext);
    // const {organizationabc, setOrganizationabc} = useState(0);
    // useEffect(() => {
    //     initialcalltoAPIs();
    // }, [])

    const initialcalltoAPIs = async () => {
        // const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));
        // const userData = await fetchUserProfile(okta.idToken.claims.sub);
        // const dataofUser = await setprofileforUser(userData.profile);
        // const isadminfecth = await checkAdmin(userData.profile.organization, userData.profile.email);
        // if (userData.profile.organization === '999999999') {
        //     const id = userData.profile.organization;
        //     localStorage.setItem('isAdmin', isadminfecth);
        // }
    }


    return (
        <Suspense fallback={<LayoutSplashScreen/>}>
            <Switch>

                <SecureRoute exact path="/dashboard" component={Dashboard}/>
                <SecureRoute exact path="/dashboard/table-sample" component={DatatableListing}/>
                <SecureRoute exact path='/dashboard/entity' component={SelfDetailedPage}/>
                <SecureRoute exact path="/dashboard/entity/:id" component={EntityDetailedPage}/>
                <SecureRoute exact path="/dashboard/entity/form/add" component={AddEntityForm}/>
                <SecureRoute exact path="/dashboard/admins" component={Admins}/>
                <SecureRoute exact path="/dashboard/contacts" component={Contacts}/>
                <SecureRoute exact path="/dashboard/attachments" component={Attachments}/>
                <SecureRoute exact path="/dashboard/contact/form/add/:id" component={AddContactForm}/>
                <SecureRoute exact path="/dashboard/attachment/form/add/:id" component={AddAttachmentForm}/>
                <SecureRoute exact path="/dashboard/agents" component={RegisteredAgents}/>
                <SecureRoute exact path="/test" component={Test}/>

            </Switch>
        </Suspense>
    )

}

export default withAuth(HomePage);
