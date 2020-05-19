import React, {useContext, useEffect} from 'react';
import Grid from "@material-ui/core/Grid";
import {Title} from '../home/helpers/titles'
import {
    Portlet,
    PortletBody,
    PortletHeader,
    PortletHeaderToolbar
} from "../../partials/content/Portlet";
import AttachmentIcon from '@material-ui/icons/Attachment';
import {makeStyles} from "@material-ui/core/styles";
import EntityListing from '../entity/EntityListing';
import Link from "@material-ui/core/Link";
import {useHistory} from "react-router-dom";
import VisibilityIcon from '@material-ui/icons/Visibility';
import {OktaUserContext} from "../../context/OktaUserContext";
import {adminList, adminCreate} from "../../crud/admin.crud";
import ContactList from "../../pages/entity/ContactList";
import MaterialTable from 'material-table';
const useStyles = makeStyles(theme => ({
    root: {
        flexGrow: 1,
    },
    paper: {
        padding: theme.spacing(2),
        textAlign: 'center',
        color: theme.palette.text.secondary,
    },

    adjustment: {
        marginRight: '5px',
    },

    companyinfo: {
        listStyle: 'none',
        padding: '0px',
        minHeight: '100px'

    },
    listItem: {
        marginBottom: '5px'
    }
}));



const Admins = () => {
    const history = useHistory();
    const classes = useStyles();
    const {oktaprofile, isAdmin} = useContext(OktaUserContext);

    const [state, setState] = React.useState({
        columns: [
            {title: 'First Name', field: 'first_name'},
            {title: 'Last Name', field: 'last_name'},
            {title: 'email', field: 'email'},
            {title: 'Last Activity', field: 'last_activity', editable: 'never'},
        ],
        data: [],
    });

    const [adminListing, setAdminListing] = React.useState([]);
    const [loading, setLoading] = React.useState(true);

    useEffect(() => {
        asyncDataFetch();
    }, [])

    const asyncDataFetch = async () => {
        await fetchData();
        setLoading(false);
    }

    const fetchData = async () => {
        const data = await adminList(oktaprofile.organization, oktaprofile.email).then(response => {
            setState({...state, data: response.result});
        })
    }


    const addAdmin = async (newData) => {
        var formdata = new FormData();
        formdata.append("first_name", newData.first_name);
        formdata.append("last_name", newData.last_name);
        formdata.append("email", newData.email);

        const result = await adminCreate(formdata);

        setState((prevState) => {
            const data = [...prevState.data];
            data.push(newData);
            return { ...prevState, data };
        })
        console.log(newData);
    }

    return (
        <>
            <Title title={'Administrator'}/>
            <Grid container spacing={1}>
                <Grid item xs={12}>
                    <Portlet fluidHeight={true}>
                        <PortletHeader icon={<AttachmentIcon className={classes.adjustment}/>} title="Admin List"/>
                        <PortletBody>
                            {/*<ContactList loading={loading} tooltip={'New admin'} redirect={true}*/}
                            {/*             url={'/dashboard/contact/form/add'} data={dummyData} title={'Admin'}/>*/}
                            <MaterialTable
                                isLoading={loading}
                                title="Admin"
                                columns={state.columns}
                                data={state.data}
                                editable={{
                                    onRowAdd: (newData) =>
                                        new Promise((resolve) => {
                                            setTimeout(() => {
                                                resolve();
                                                addAdmin(newData);
                                            }, 600);
                                        }),
                                    onRowUpdate: (newData, oldData) =>
                                        new Promise((resolve) => {
                                            setTimeout(() => {
                                                resolve();
                                                if (oldData) {
                                                    setState((prevState) => {
                                                        const data = [...prevState.data];
                                                        data[data.indexOf(oldData)] = newData;
                                                        return { ...prevState, data };
                                                    });
                                                }
                                            }, 600);
                                        }),
                                    onRowDelete: (oldData) =>
                                        new Promise((resolve) => {
                                            setTimeout(() => {
                                                resolve();
                                                setState((prevState) => {
                                                    const data = [...prevState.data];
                                                    data.splice(data.indexOf(oldData), 1);
                                                    return { ...prevState, data };
                                                });
                                            }, 600);
                                        }),
                                }}
                            />
                        </PortletBody>
                    </Portlet>
                </Grid>
            </Grid>
        </>
    )
}

export default Admins;
