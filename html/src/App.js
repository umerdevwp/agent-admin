import React, {Component} from 'react';
import {BrowserRouter as Router} from 'react-router-dom';
import AppWithRouterAccess from './components/routes/AppWithRouterAccess';
import './App.css'

class App extends Component {
    render() {
        return (
            <div id="messageModal">
                <Router>
                    <AppWithRouterAccess/>
                </Router>
            </div>
        );
    }
}

export default App;
