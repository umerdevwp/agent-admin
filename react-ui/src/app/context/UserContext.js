import React, {createContext, useState, useEffect, useLayoutEffect} from 'react'
import {withAuth} from "@okta/okta-react";
import {userInfoAPI, fetchUserProfile} from "../crud/auth.crud";

export const UserContext = createContext(
    {
        authentication: '',
        isAdmin: '',
        zohoID: '',
        firstName: '',
        lastName: '',
        id: '',
        token: '',
        loading: false,
        profile:'',
        userProfileNew: {}
    }
);

function UserContextProvider(props) {
    const [userProfileNew, setUserProfileNew] = useState({});
    const [appLoader, setAppLoader] = useState({
        loading: false
    });
    const [auth, setAuth] = useState(
        {
            authentication: false,
        }
    );
    const [userInfo, setUserInfoState] = useState({})
    const [state, setState] = useState(
        {
            isAdmin: '',
            token: '',
        }
    );
    const [userID, setUserID] = useState({
        zohoID: ''
    })

    useEffect(() => {
        justToCheck();
    }, [auth.authentication]);

    const justToCheck = async () => {
        setAppLoader({...appLoader, loading: true});
        const AuthReturnedValue = await authentication();
        setAuth({authentication: AuthReturnedValue});
        const tokenReturned = await getToken();
        if (auth.authentication) {
            try {
                const profile = await getUserValue();
                const newObject = {
                    profile: profile
                }
                setState({...state, ...newObject})
                await userProfile(tokenReturned);
                setAppLoader({...appLoader, loading: false});
            } catch (err) {
                props.auth.login('/');
            }
        }
    }

    const authToggle = (value = {}) => {
        setAuth({...state, ...value})
    }

    const userProfileInitialializer = (value = {}) => {
        setUserInfoState({...state, ...value})
    }
    const userInfoInitializer = (value = {}) => {
        setState({...state, ...value})
    }

    const authentication = async (value) => {
        return Promise.resolve(props.auth.isAuthenticated())
    }

    const getUserValue = async () => {
        return Promise.resolve(props.auth.getUser())
    }

    const getToken = async () => {
        return Promise.resolve(props.auth.getAccessToken())
    }


    const setprofileforUser = (value = {}) => {
        setUserProfileNew(value)
    }

    const userProfile = async (token) => {
        const finaltoken = localStorage.getItem('accessToken') === '' ? token : localStorage.getItem('accessToken')
        const userInfoReturn = await userInfoAPI(finaltoken);
        const fetchUserProfileReturn = await fetchUserProfile(userInfoReturn.sub);
        setUserID({...userID, zohoID: fetchUserProfileReturn.profile.organization})

    }

    return (
        <UserContext.Provider
            value={{
                ...state,
                authentication,
                userInfoInitializer,
                userProfileInitialializer,
                ...userInfo,
                ...auth,
                ...appLoader,
                authToggle,
                ...userID,
                setprofileforUser,
                ...userProfileNew
            }}>
            {props.children}
        </UserContext.Provider>
    )

}

export default withAuth(UserContextProvider)
