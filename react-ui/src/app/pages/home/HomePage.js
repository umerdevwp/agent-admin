import React, {Suspense, lazy} from "react";
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

function HomePage() {

    return (
        <Suspense fallback={<LayoutSplashScreen/>}>
            <Switch>
                <SecureRoute exact path="/dashboard" component={Dashboard}/>
                <SecureRoute exact path="/dashboard/entity/:id" component={EntityDetailedPage}/>
                <SecureRoute exact path="/dashboard/entity/form/add" component={AddEntityForm}/>
                <SecureRoute exact path="/dashboard/admins" component={Admins}/>
                <SecureRoute exact path="/dashboard/contacts" component={Contacts}/>
                <SecureRoute exact path="/dashboard/attachments" component={Attachments}/>
                <SecureRoute exact path="/dashboard/contact/form/add" component={AddContactForm}/>
                <SecureRoute exact path="/dashboard/attachment/form/add" component={AddAttachmentForm}  />
                <SecureRoute exact path="/dashboard/agents" component={RegisteredAgents}  />

                <SecureRoute exact path="/test" component={Test}/>
            </Switch>
        </Suspense>
    );
}

export default withAuth(HomePage);
