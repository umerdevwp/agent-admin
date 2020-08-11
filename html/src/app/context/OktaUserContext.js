import React, {createContext, useState, useEffect, useLayoutEffect, useCallback, useMemo} from 'react'
import {userInfoAPI, fetchUserProfile} from "../crud/auth.crud";
import {checkAdmin, entityList, checkRole} from '../crud/enitity.crud';
import {withAuth} from '@okta/okta-react';

export const OktaUserContext = createContext(
    {
        oktaprofile: {},
        isAdmin: {isAdmin: false},
        loading: {},
        hasChild: false,
        errorList: [],
        organization_parent: ''


    }
);

function OktaUserContextProvider(props) {

    useEffect(() => {
        setTimeout(() => {
            const okta = localStorage.getItem('okta-token-storage');
            if (okta !== '{}' && okta !== null && okta !== undefined) {
                getUsefullinfo();
            }
        }, 3000)
    }, []);

    const [oktaprofile, setOktaprofile] = useState({});
    const [isAdmin, setisAdmin] = useState({});
    const [hasChild, setHasChild] = useState({});
    const [entityDashboardList, setEntityDashboardList] = useState([]);
    const [role, setRole] = useState({});
    const [errorList, setErrorList] = useState([]);
    const [loading, setLoading] = useState({
        loading: true
    })


    const getUsefullinfo = async () => {

        const profile_data = await profileDetails()
        await getUserRole(profile_data);
        setLoading({...loading, loading: false});

    }


    const profileDetails = async () => {
        const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));
        const data = await fetchUserProfile(okta.idToken.claims.sub);
        setOktaprofile({...oktaprofile, oktaprofile: data.profile})
        return Promise.resolve(data.profile);
    }


    const getUserRole = async (profile) => {
        console.log(profile);
        var organization_parent = '';
        if(profile.organization_parent) {
            const bit = profile.organization_parent.toLowerCase();
            if(bit === 'yes'){
                organization_parent = 1;
            }
        }
        const get_role = await checkRole(profile.organization, organization_parent ? organization_parent : 0);
        if (get_role) {
            if (get_role.status === true) {
                return Promise.resolve(setRole({...role, role: get_role.data.role}));
            }
            if (get_role.status === false) {
                return Promise.resolve(setErrorList({...errorList, errorList: get_role.message}));
            }
        }
    }


    const checkifAdmin = async (profile) => {
        const response = await checkAdmin(profile.organization, profile.email);
        setisAdmin({...isAdmin, isAdmin: response});
    }

    const organization_parent = async (profile) => {
        setErrorList({...errorList, errorList: profile});
    }


    const addError = (data) => {
        setErrorList(errorList => [...errorList, data])
    }


    return (
        <OktaUserContext.Provider
            value={{
                ...oktaprofile,
                ...isAdmin,
                ...loading,
                ...role,
                errorList,
                addError
            }}>
            {props.children}
        </OktaUserContext.Provider>
    )

}

export default withAuth(OktaUserContextProvider);
