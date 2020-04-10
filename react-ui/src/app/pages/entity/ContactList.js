import React, {useContext, useEffect} from 'react';
import MaterialTable from 'material-table';
import Skeleton from '@material-ui/lab/Skeleton';
import Link from '@material-ui/core/Link';
import Paper from '@material-ui/core/Paper';
import {makeStyles} from '@material-ui/core/styles';
import {
    withRouter,
    Redirect
} from 'react-router-dom';
import {useHistory} from "react-router-dom";
import VisibilityIcon from '@material-ui/icons/Visibility';
import Grid from "@material-ui/core/Grid";
import {UserContext} from "../../context/UserContext";
import {OktaUserContext} from "../../context/OktaUserContext";
import {entityList, entityListingAxios} from "../../crud/enitity.crud";
import {fetchUserProfile} from "../../crud/auth.crud";


const useStyles = makeStyles(theme => ({
    root: {
        width: '100%',
        marginTop: theme.spacing(3),
        overflowX: 'auto',
    },
    table: {
        minWidth: 650,
    },
}));

function ContactList(props) {
    const {oktaprofile, isAdmin} = useContext(OktaUserContext);
    const [state, setState] = React.useState('');
    const history = useHistory();
    const classes = useStyles();
    const settingData = props.data


    const handleUpdate = (newData) => {
        return Promise.resolve(console.log(newData));
    }


    return (

        <Grid item xs={12}>
            <MaterialTable
                isLoading={props.loading}
                actions={[
                    {
                        icon: 'add',
                        tooltip: props.tooltip ? props.tooltip : 'Add User',
                        isFreeAction: true,
                        onClick: (event) => {
                            if (props.redirect) {
                                history.push(props.url);
                            }
                        }
                    }
                ]}
                title={props.title !== '' ? props.title : ''}
                columns={settingData.columns}
                data={settingData.data}
                editable={isAdmin ? {
                    // onRowAdd: newData =>
                    //     new Promise(resolve => {
                    //         setTimeout(() => {
                    //             resolve();
                    //             setState(prevState => {
                    //                 const data = [...prevState.data];
                    //                 data.push(newData);
                    //                 return {...prevState, data};
                    //             });
                    //
                    //
                    //
                    //         }, 600);
                    //     }),
                    onRowUpdate: (newData, oldData) =>
                        new Promise(resolve => {
                            setTimeout(() => {
                                resolve();
                                handleUpdate(newData)
                                if (oldData) {
                                    setState(prevState => {
                                        const data = [...prevState.data];
                                        data[data.indexOf(oldData)] = newData;
                                        return {...prevState, data};
                                    });
                                }
                            }, 600);
                        }),
                    onRowDelete: oldData =>
                        new Promise(resolve => {
                            setTimeout(() => {
                                resolve();
                                setState(prevState => {
                                    const data = [...prevState.data];
                                    data.splice(data.indexOf(oldData), 1);
                                    return {...prevState, data};
                                });
                            }, 600);
                        }),
                } : ''}
            />
        </Grid>

    )
}


export default withRouter(ContactList);
