import React, {createContext, useState, useEffect, useLayoutEffect} from 'react'
import {userInfoAPI, fetchUserProfile} from "../crud/auth.crud";
import {checkAdmin} from '../crud/enitity.crud';
import {withAuth} from '@okta/okta-react';

export const OktaUserContext = createContext(
    {
        oktaprofile: {},
        isAdmin: {},
        loading: {}

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
    const [loading, setLoading] = useState({
        loading: true
    })


    const getUsefullinfo = async () => {

        const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));
        const data = await fetchUserProfile(okta.idToken.claims.sub);
        setOktaprofile({...oktaprofile, oktaprofile: data.profile});
        checkifAdmin(data.profile)


    }

    const checkifAdmin = async (profile) => {
        const response = await checkAdmin(profile.organization, profile.email);
        setisAdmin({...isAdmin, isAdmin: response});
        setLoading({...loading, loading: false});
    }


    return (
        <OktaUserContext.Provider
            value={{
                ...oktaprofile,
                ...isAdmin,
                ...loading
            }}>
            {props.children}
        </OktaUserContext.Provider>
    )

}

export default withAuth(OktaUserContextProvider);
