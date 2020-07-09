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

import {OktaUserContext} from "../../context/OktaUserContext";
import {entityList, entityListingAxios} from "../../crud/enitity.crud";
import {fetchUserProfile} from "../../crud/auth.crud";
import EntityDetailedPage from "./EntityDetailedPage";

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
<<<<<<< HEAD
    const {oktaprofile, isAdmin, entityDashboardList, role} = useContext(OktaUserContext);
=======
    const {oktaprofile, isAdmin, entityDashboardList, hasChild} = useContext(OktaUserContext);
>>>>>>> 9400987a155f3a0a079c8ab996efdb562d72857d
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
    }

    const fetchData = async () => {
        const data = await entityList().then(response => {
            if (response.data) {
                setEntityData(response.data.results);
                setLoading(false);
            }
<<<<<<< HEAD

            if(response.error){
                entityDashboardList(response.error.message)
            }


=======
>>>>>>> 9400987a155f3a0a079c8ab996efdb562d72857d
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
            {title: 'Name', field: 'name'},
            {title: 'Entity Structure', field: 'entityStructure'},
            {title: 'Filing State', field: 'filingState'},
            {title: 'Formation Date', field: 'formationDate'},
        ],
        data: entitydata,
    };

    const handleUpdate = (newData) => {
        return Promise.resolve(console.log(newData));
    }
    return (

        <Grid item xs={12}>
<<<<<<< HEAD
            { role === 'Parent Organization' ?
            <div style={{maxWidth: "100%"}}>
                <MaterialTable
=======
            <div style={{maxWidth: "100%"}}>
                {hasChild ? <MaterialTable
>>>>>>> 9400987a155f3a0a079c8ab996efdb562d72857d
                    isLoading={loading}
                    actions={isAdmin === true ? [
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
                    ] : ''}
                    title={props.title !== '' ? props.title : ''}
                    columns={settingData.columns}
                    data={settingData.data}
                    options={{
                        grouping: true
                    }}
                    editable={isAdmin ? {
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
<<<<<<< HEAD
                />
            </div> : '' }

            {
                role === 'Child Entity' ?
                    <EntityDetailedPage entityid={oktaprofile.organization} /> : ''
            }



            {
              role === 'Administrator' ?
                  <div style={{maxWidth: "100%"}}>
                      <MaterialTable
                          isLoading={loading}
                          actions={isAdmin === true ? [
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
                          ] : ''}
                          title={props.title !== '' ? props.title : ''}
                          columns={settingData.columns}
                          data={settingData.data}
                          options={{
                              grouping: true
                          }}
                          editable={isAdmin ? {
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
                  </div> : ''

            }


=======
                /> : <EntityDetailedPage breadcrumbz={false} entityid={oktaprofile.organization} />}
            </div>
>>>>>>> 9400987a155f3a0a079c8ab996efdb562d72857d
        </Grid>

    )
}


export default withRouter(EntityListing);
