/* eslint-disable no-script-url,jsx-a11y/anchor-is-valid */
import React, {useContext} from "react";
import axios from "axios";
import { Link } from "react-router-dom";
import Dropdown from "react-bootstrap/Dropdown";
import { toAbsoluteUrl } from "../../../_metronic";
import HeaderDropdownToggle from "../content/CustomDropdowns/HeaderDropdownToggle";
import {withAuth} from "@okta/okta-react";
import {UserContext} from "../../context/UserContext";
import {userInfoAPI, fetchUserProfile} from "../../crud/auth.crud";


class UserProfile extends React.Component {

  constructor() {
    super();
    this.state = {
      profile: {}
    }

  }

  componentDidMount() {
    var okta = JSON.parse(localStorage.getItem('okta-token-storage'));
    this.setState({profile: okta.idToken.claims})
  }




  render() {
    const { user, showHi, showAvatar, showBadge } = this.props;
    const name = this.state.profile.name;
    if(name) {
      var initials = name.match(/\b\w/g) || [];
      initials = ((initials.shift() || '') + (initials.pop() || '')).toUpperCase();

    }

    return (
      <Dropdown className="kt-header__topbar-item kt-header__topbar-item--user" drop="down" alignRight>
        <Dropdown.Toggle
          as={HeaderDropdownToggle}
          id="dropdown-toggle-user-profile"
        >
          <div className="kt-header__topbar-user">
            {showHi && (
              <span className="kt-header__topbar-welcome kt-hidden-mobile">
                Hi,
              </span>
            )}

            {showHi && (
              <span className="kt-header__topbar-username kt-hidden-mobile">
                {this.state.profile.name}
              </span>
            )}

            {/*{showAvatar && <img alt="Pic" src={user.pic} />}*/}

            {showBadge && (
              <span className="kt-badge kt-badge--username kt-badge--unified-success kt-badge--lg kt-badge--rounded kt-badge--bold">
                {/* TODO: Should get from currentUser */}
                John Doe
              </span>
            )}
          </div>
        </Dropdown.Toggle>
        <Dropdown.Menu className="dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-xl">
          {/** ClassName should be 'dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-xl' */}
          <div
            className="kt-user-card kt-user-card--skin-dark kt-notification-item-padding-x"
            style={{
              backgroundImage: `url(${toAbsoluteUrl("/media/misc/bg-1.jpg")})`
            }}
          >
            <div className="kt-user-card__avatar">
              <img alt="Pic" className="kt-hidden" src={''} />
              <span className="kt-badge kt-badge--lg kt-badge--rounded kt-badge--bold kt-font-success">
                {initials ? initials: ''}
              </span>
            </div>
            <div className="kt-user-card__name">{this.state.profile.name}</div>
            <div className="kt-user-card__badge">
              <span className="btn btn-success btn-sm btn-bold btn-font-md">
                23 messages
              </span>
            </div>
          </div>
          <div className="kt-notification">
            <a className="kt-notification__item">
              <div className="kt-notification__item-icon">
                <i className="flaticon2-calendar-3 kt-font-success" />
              </div>
              <div className="kt-notification__item-details">
                <div className="kt-notification__item-title kt-font-bold">
                  My Profile
                </div>
                <div className="kt-notification__item-time">
                  Account settings and more
                </div>
              </div>
            </a>
            <a className="kt-notification__item">
              <div className="kt-notification__item-icon">
                <i className="flaticon2-mail kt-font-warning" />
              </div>
              <div className="kt-notification__item-details">
                <div className="kt-notification__item-title kt-font-bold">
                  My Messages
                </div>
                <div className="kt-notification__item-time">
                  Inbox and tasks
                </div>
              </div>
            </a>
            <a className="kt-notification__item">
              <div className="kt-notification__item-icon">
                <i className="flaticon2-rocket-1 kt-font-danger" />
              </div>
              <div className="kt-notification__item-details">
                <div className="kt-notification__item-title kt-font-bold">
                  My Activities
                </div>
                <div className="kt-notification__item-time">
                  Logs and notifications
                </div>
              </div>
            </a>
            <a className="kt-notification__item">
              <div className="kt-notification__item-icon">
                <i className="flaticon2-hourglass kt-font-brand" />
              </div>
              <div className="kt-notification__item-details">
                <div className="kt-notification__item-title kt-font-bold">
                  My Tasks
                </div>
                <div className="kt-notification__item-time">
                  latest tasks and projects
                </div>
              </div>
            </a>
            <div className="kt-notification__custom">

               <button className={'btn btn-primary'} onClick={()=>{this.props.auth.logout('/')}}>Sign Out</button>

            </div>
          </div>
        </Dropdown.Menu>
      </Dropdown>
    );
  }
}

// const mapStateToProps = ({ auth: { user } }) => ({
//   user
// });

export default withAuth(UserProfile);
