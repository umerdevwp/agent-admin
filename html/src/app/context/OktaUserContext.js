import React, {createContext, useState, useEffect, useLayoutEffect, useCallback, useMemo} from 'react'
import {userInfoAPI, fetchUserProfile} from "../crud/auth.crud";
<<<<<<< HEAD
import {checkAdmin, entityList, checkRole} from '../crud/enitity.crud';
=======
import {checkAdmin, entityList} from '../crud/enitity.crud';
>>>>>>> 9400987a155f3a0a079c8ab996efdb562d72857d
import {withAuth} from '@okta/okta-react';

export const OktaUserContext = createContext(
    {
        oktaprofile: {},
<<<<<<< HEAD
        isAdmin: {isAdmin: false},
        loading: {},
        hasChild: false,
        errorList: [],
        organization_parent: ''

=======
        isAdmin: false,
        loading: {},
        entityDashboardList: {},
        hasChild: false
>>>>>>> 9400987a155f3a0a079c8ab996efdb562d72857d

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
<<<<<<< HEAD
    const [role, setRole] = useState({});
    const [errorList, setErrorList] = useState([]);
=======
>>>>>>> 9400987a155f3a0a079c8ab996efdb562d72857d
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
<<<<<<< HEAD
        setOktaprofile({...oktaprofile, oktaprofile: data.profile})
        return Promise.resolve(data.profile);
    }
=======
        setOktaprofile({...oktaprofile, oktaprofile: data.profile});
        await checkifAdmin(data.profile);
        await getEntityListing();
        setLoading({...loading, loading: false});

    }

    const getEntityListing = async () => {
        const entity_list = await entityList();

        if (entity_list.data) {
            const results = entity_list.data.results;
>>>>>>> 9400987a155f3a0a079c8ab996efdb562d72857d

            if (results.length > 0) {
                localStorage.setItem('hasChild', true);
                return Promise.resolve(setHasChild({...hasChild, hasChild: true}));

<<<<<<< HEAD
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
=======
            } else {
                return Promise.resolve(setHasChild({...hasChild, hasChild: false}));
            }

>>>>>>> 9400987a155f3a0a079c8ab996efdb562d72857d
        }
    }


    const checkifAdmin = async (profile) => {
        const response = await checkAdmin(profile.organization, profile.email);
<<<<<<< HEAD
        setisAdmin({...isAdmin, isAdmin: response});
    }

    const organization_parent = async (profile) => {
        setErrorList({...errorList, errorList: profile});
    }


    const addError = (data) => {
        setErrorList(errorList => [...errorList, data])
=======
        return Promise.resolve(setisAdmin({...isAdmin, isAdmin: response}));
>>>>>>> 9400987a155f3a0a079c8ab996efdb562d72857d
    }


    return (
        <OktaUserContext.Provider
            value={{
                ...oktaprofile,
                ...isAdmin,
                ...loading,
<<<<<<< HEAD
                ...role,
                errorList,
                addError
=======
                ...entityDashboardList,
                ...hasChild
>>>>>>> 9400987a155f3a0a079c8ab996efdb562d72857d
            }}>
            {props.children}
        </OktaUserContext.Provider>
    )

}

export default withAuth(OktaUserContextProvider);
