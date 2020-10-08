import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import {withOktaAuth} from '@okta/okta-react';
import Layout from "../layout/Layout";

export default withOktaAuth(class Home extends Component {
    constructor(props) {
        super(props);
        this.login = this.login.bind(this);
        this.logout = this.logout.bind(this);
    }

    async login() {
        this.props.authService.login();
    }

    async logout() {
        this.props.authService.logout('/');
    }

    render() {
        if (this.props.authState.isPending) return null;

        const button = this.props.authState.isAuthenticated ?
            <button onClick={this.logout}>Logout</button> :
            <button onClick={this.login}>Login</button>;

        return (
            <Layout>
            <div>
                <Link to='/'>Home</Link><br/>
                <Link to='/protected'>Protected</Link><br/>
                <Link to='/dashboard'>Dashboard</Link><br/>
                {button}
            </div>
            </Layout>
        );
    }
});
