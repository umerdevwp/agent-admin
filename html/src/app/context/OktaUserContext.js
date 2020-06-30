import React, {createContext, useState, useEffect, useLayoutEffect} from 'react'
import {userInfoAPI, fetchUserProfile} from "../crud/auth.crud";
import {checkAdmin, entityList} from '../crud/enitity.crud';
import {withAuth} from '@okta/okta-react';

export const OktaUserContext = createContext(
    {
        oktaprofile: {},
        isAdmin: false,
        loading: {},
        entityDashboardList: {},
        hasChild: false

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
    const [loading, setLoading] = useState({
        loading: true
    })


    const getUsefullinfo = async () => {

        const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));
        const data = await fetchUserProfile(okta.idToken.claims.sub);
        setOktaprofile({...oktaprofile, oktaprofile: data.profile});
        await checkifAdmin(data.profile);
        await getEntityListing();
        setLoading({...loading, loading: false});

    }

    const getEntityListing = async () => {
        const entity_list = await entityList();

        if (entity_list.data) {
            const results = entity_list.data.results;

            if (results.length > 0) {
                localStorage.setItem('hasChild', true);
                return Promise.resolve(setHasChild({...hasChild, hasChild: true}));

            } else {
                return Promise.resolve(setHasChild({...hasChild, hasChild: false}));
            }

        }
    }


    const checkifAdmin = async (profile) => {
        const response = await checkAdmin(profile.organization, profile.email);
        return Promise.resolve(setisAdmin({...isAdmin, isAdmin: response}));
    }


    return (
        <OktaUserContext.Provider
            value={{
                ...oktaprofile,
                ...isAdmin,
                ...loading,
                ...entityDashboardList,
                ...hasChild
            }}>
            {props.children}
        </OktaUserContext.Provider>
    )

}

export default withAuth(OktaUserContextProvider);
