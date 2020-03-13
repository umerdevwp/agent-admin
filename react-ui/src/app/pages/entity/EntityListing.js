import React from 'react';
import MaterialTable from 'material-table';
import Skeleton from '@material-ui/lab/Skeleton';
import Link from '@material-ui/core/Link';
import Paper from '@material-ui/core/Paper';
import { makeStyles } from '@material-ui/core/styles';
import {
    withRouter,
    Redirect
} from 'react-router-dom';
import {useHistory} from "react-router-dom";
import VisibilityIcon from '@material-ui/icons/Visibility';
import Grid from "@material-ui/core/Grid";
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

    const classes = useStyles();



    const dummyData = {
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
            {title: 'Name', field: 'name'},
            {title: 'Entity Structure', field: 'entity_structure'},
            {title: 'Filing State', field: 'filing_state'},
            {title: 'Formation Date', field: 'formation_date'},
        ],
        data: [
            {id: 1, name: 'Mehmet', entity_structure: 'Baran', filing_state: 'AK', formation_date: '2020-12-01'},
            {id: 2, name: 'ASASAehmet', entity_structure: 'Baran', filing_state: 'AK', formation_date: '2019-12-07'},
            {id: 3, name: 'MehAmet', entity_structure: 'Baran', filing_state: 'AK', formation_date: '2019-12-07'},
            {id: 5, name: 'Mehmet', entity_structure: 'Baran', filing_state: 'AK', formation_date: '2019-12-07'},
            {id: 6, name: 'Mehmet', entity_structure: 'Baran', filing_state: 'AK', formation_date: '2019-12-07'},
            {id: 7, name: 'Mehmet', entity_structure: 'Baran', filing_state: 'AK', formation_date: '2019-12-07'},
            {id: 8, name: 'Mehmet', entity_structure: 'Baran', filing_state: 'AK', formation_date: '2019-12-07'},
            {id: 9, name: 'Mehmet', entity_structure: 'Baran', filing_state: 'AK', formation_date: '2019-12-07'},
            {id: 10, name: 'Mehmet', entity_structure: 'Baran', filing_state: 'AK', formation_date: '2019-12-07'},
            {id: 11, name: 'Mehmet', entity_structure: 'Baran', filing_state: 'AK', formation_date: '2019-12-07'},
            {id: 12, name: 'Mehmet', entity_structure: 'Baran', filing_state: 'AK', formation_date: '2019-12-07'},
            {id: 13, name: 'Mehmet', entity_structure: 'Baran', filing_state: 'AK', formation_date: '2019-12-07'},
        ],
    };
    const history = useHistory();

    const [loading, setLoading] = React.useState(true);
    const [user, setUser] = React.useState({
        isAdmin: true
    });

    const [state, setState] = React.useState(props.data !== undefined ? props.data : dummyData);




    React.useEffect(() => {

        loadingToggle();

    }, [])

    const loadingToggle = () => {
        setTimeout(() => {
            setLoading(false);
        }, 2000);
    }


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
                            if(props.redirect) {
                                history.push(props.url);
                            }
                        }
                    }
                ]}
                title={props.title !== '' ? props.title : ''}
                columns={state.columns}
                data={state.data}
                options={{
                    grouping: true
                }}
                editable={user.isAdmin ? {
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
