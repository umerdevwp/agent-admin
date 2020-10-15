import React, {createContext, useState} from 'react'
import {withOktaAuth} from '@okta/okta-react';
import {useOktaAuth} from "@okta/okta-react";
import {fetchUserProfile} from "../api/OKTA";
import {checkRole} from '../api/enitity.crud';


export const UserContext = createContext(
    {
        drawerState: true,
        role: '',
        title: '',
        token: '',
        profile: '',
        attributes: [],
        loading: false,
        errorList:[],
        currentEntity:[]
    }
);

export const UserConsumer = UserContext.Consumer;

function UserContextProvider(props) {
    const {authState, authService} = useOktaAuth();
    const [title, setTitle] = useState('');
    const [drawerState, setDrawerState] = useState(true);
    const [token, setToken] = useState('');
    const [profile, setProfile] = useState([]);
    const [attributes, setAttributes] = useState([]);
    const [appLoader, setAppLoader] = useState({loading: false});
    const [errorList, setErrorList] = useState([]);
    const [currentEntity, setCurrentEntity] = useState([]);
    const [role, setRole] = useState({});
    React.useEffect(() => {
        const okta = localStorage.getItem('okta-token-storage');
        if (okta !== '{}' && okta !== null && okta !== undefined) {

            initialUtliz();
        }
    }, []);


    React.useEffect(()=>{
        if (errorList !== undefined || errorList.length !== 0) {
            errorList.map((value, index) => {
               setTimeout(()=>{
                   removeError(index);
               }, 6000)
            })
        }

    },[errorList])


    const initialUtliz = async () => {
        localStorage.removeItem('role');
        const tokenOKTA = await getToken();
        console.log('Auth in Context',authState.isAuthenticated);
        // if(tokenOKTA === null){
        //  authService.logout('/');
        // }
        const result_profile = await getUserProfile();
        const result_attributes = await getUserAttributes(result_profile);
        if (result_attributes) {
            await getUserRole(result_attributes.profile, tokenOKTA);
        }

        setInterval(()=>{
            setAppLoader({...appLoader, loading: true});
        },4000)


    }


    const getToken = async () => {
        const tokenResult = await authState.accessToken;
        setToken(tokenResult);
        return Promise.resolve(tokenResult);
    }


    const getUserProfile = async () => {

        const tokenResult = await authService.getUser();
        if (tokenResult) {
            await setProfile({...profile, profile: tokenResult});
        }
        return Promise.resolve(tokenResult);
    }


    const getUserAttributes = async (UserData) => {
        if (UserData) {
            const AllAttributes = await fetchUserProfile(UserData.sub);
            if (AllAttributes) {
                await setAttributes({...attributes, attributes: AllAttributes.profile})
            }
            return Promise.resolve(AllAttributes);
        }
    }

    const getUserRole = async (profile, tokenOKTA) => {
        var organization_parent = '';
        if (profile.organization_parent) {
            const bit = profile.organization_parent.toLowerCase();
            if (bit === 'yes') {
                organization_parent = 1;
            }
        }
        try {
            const get_role = await checkRole(profile.organization, organization_parent ? organization_parent : 0, tokenOKTA);

            if (get_role) {
                if (get_role.status === true) {
                    await localStorage.setItem('role', get_role.data.role);
                    return Promise.resolve(setRole({...role, role: get_role.data.role}));
                }
                if (get_role.status === false) {
                    return Promise.resolve(addError(get_role.message));
                }
            }

            if(get_role.type === 'error'){
                window.location.reload();
            }

            if(get_role.status === 401){
                window.location.reload();
            }


            if (get_role.message) {
                addError(get_role.message)
            }


        } catch (e) {

        }

    }

    const addError = (data) => {
        setErrorList(errorList => [...errorList, data])
    }

    const addTitle = (data) => {
        setTitle(data);
    }

    const updateCurrentEntity = (data) => {
         setCurrentEntity(data)
    }


    const removeError = (index) => {
        var array = [...errorList]; // make a separate copy of the array
        if (index !== -1) {
            array.splice(index, 1);
            setErrorList(array)
        }

    }

    const changeDrawer = (data) => {
        setDrawerState(data);
    }


    return (
        <UserContext.Provider
            value={{
                ...token,
                ...profile,
                ...attributes,
                ...appLoader,
                ...role,
                title,
                errorList,
                addError,
                addTitle,
                drawerState,
                changeDrawer,
                removeError,
                updateCurrentEntity,
                currentEntity
            }}>
            {props.children}
        </UserContext.Provider>
    )

}

export default withOktaAuth(UserContextProvider);
