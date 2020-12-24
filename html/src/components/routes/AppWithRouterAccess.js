import React, {Component} from 'react';
import {Route, withRouter} from 'react-router-dom';
import {Security, SecureRoute, LoginCallback} from '@okta/okta-react';
import Home from '../okta/Home';
import Login from '../okta/Login';
import AddEntity from "../entity/AddEntity";
import Dashboard from '../dashboard/Dashboard';
import UserContextProvider from '../context/UserContext';
import EntityDetailedPage from '../entity/EntityDetailedPage';
import SelfDetailedPage from '../entity/SelfDetailedPage';
import AddContactForm from "../contact/AddContactForm";
import Attachments from "../attachment/Attachments";
import Contacts from "../contact/Contacts";
import AddAttachmentForm from "../attachment/AddAttachmentForm";
import Privacy from "../privacy/Privacy";
import ExampleTable from "../entity/ExampleTable";
import AdminAddAttachmentForm from "../attachment/AdminAttachmentForm";
import MessageLog from '../message/MessageLog';
export default withRouter(class AppWithRouterAccess extends Component {
    constructor(props) {
        super(props);
        this.onAuthRequired = this.onAuthRequired.bind(this);
    }

    onAuthRequired() {
        this.props.history.push('/login')
    }

    render() {
        return (
            <Security
                issuer={process.env.REACT_APP_OKTA_BASE_URL + 'oauth2/default'}
                clientId={process.env.REACT_APP_OKTA_CLIENT_ID}
                redirectUri={window.location.origin + '/implicit/callback/'}
                onAuthRequired={this.onAuthRequired}
                pkce={true}>
                <UserContextProvider>
                    <SecureRoute exact={true} path='/' component={Dashboard}/>
                    <SecureRoute exact={true} path='/home' component={Home}/>
                    <SecureRoute exact={true} path='/entity' component={SelfDetailedPage}/>
                    <SecureRoute exact={true} path='/entity/:id' component={EntityDetailedPage}/>
                    <SecureRoute exact={true} path='/entity/new/add' component={AddEntity}/>
                    <SecureRoute exact={true} path='/contact/form/add' component={AddContactForm}/>
                    <SecureRoute exact={true} path='/contact/form/add/:id'
                                 component={AddContactForm}/>
                    <SecureRoute exact={true} path='/attachments' component={Attachments}/>
                    <SecureRoute exact={true} path='/contacts' component={Contacts}/>
                    <SecureRoute exact={true} path="/attachment/form/add" component={AddAttachmentForm}/>
                    <SecureRoute exact={true} path="/attachment/form/add/:id" component={AddAttachmentForm}/>
                    <SecureRoute exact={true} path="/table" component={ExampleTable}/>
                    <SecureRoute exact={true} path="/admin/attachments" component={AdminAddAttachmentForm}/>
                    <SecureRoute exact={true} path="/message/logs" component={MessageLog}/>
                </UserContextProvider>
                <Route exact={true} path='/login'
                       render={() => <Login baseUrl={process.env.REACT_APP_OKTA_BASE_URL}/>}/>
                <Route exact={true} path='/implicit/callback' component={LoginCallback}/>
                <Route exact={true} path='/privacy-policy' component={Privacy}/>
            </Security>
        );
    }
});
