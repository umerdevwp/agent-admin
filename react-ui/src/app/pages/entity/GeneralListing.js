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

function EntityListing(props) {
    const {oktaprofile, isAdmin} = useContext(OktaUserContext);
    const [state, setState] = React.useState('');
    const [entitydata, setEntityData] = React.useState([]);
    const [loading, setLoading] = React.useState(true);
    const history = useHistory();
    const classes = useStyles();


    useEffect(() => {
            asyncDataFetch();
    }, [])

    const asyncDataFetch = async () => {
            await fetchData();
            setLoading(false);
    }

    const fetchData = async () => {
        const data = await entityList(oktaprofile.organization, oktaprofile.email).then(response => {
            setEntityData(response);
        })
    }

    const settingData = {
        columns: [
            {
                render: rowData => <Link
                    component="button"
                    variant="body2"
                    onClick={() => {
                        history.push(`/dashboard/entity/${rowData.id}`);
                    }}>
                    <VisibilityIcon/>
                </Link>
            },
            {title: 'Name', field: 'entity_name'},
            {title: 'Entity Structure', field: 'entity_structure'},
            {title: 'Filing State', field: 'filing_state'},
            {title: 'Formation Date', field: 'created_time'},
        ],
        data: entitydata,
    };


    const handleUpdate = (newData) => {
        return Promise.resolve(console.log(newData));
    }


    return (

        <Grid item xs={12}>
            <MaterialTable
                isLoading={loading}
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
                options={{
                    grouping: true
                }}
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


export default withRouter(EntityListing);
