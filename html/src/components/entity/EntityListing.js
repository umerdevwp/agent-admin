import React, {useContext, useEffect} from 'react';
import MaterialTable from 'material-table';
import Link from '@material-ui/core/Link';
import {makeStyles} from '@material-ui/core/styles';
import {withRouter} from 'react-router-dom';
import {useHistory} from "react-router-dom";
import VisibilityIcon from '@material-ui/icons/Visibility';
import Grid from "@material-ui/core/Grid";

import {entityList} from "../../crud/enitity.crud";
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
    const {oktaprofile, isAdmin, entityDashboardList, role} = useContext(OktaUserContext);
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

            if(response.error){
                entityDashboardList(response.error.message)
            }


        })
    }

    const settingData = {
        columns: [

            {title: 'Name', field: 'name'},
            {title: 'Entity Structure', field: 'entityStructure'},
            {title: 'Filing State', field: 'filingState'},
            {title: 'Formation Date', field: 'formationDate'},
            {
                render: rowData => <Link
                    component="button"
                    variant="body2"
                    onClick={() => {
                        if(rowData.id !== oktaprofile.organization) {
                            history.push(`/entity/${rowData.id}`);
                        } else {
                            history.push(`/entity`);
                        }
                    }}>
                    <VisibilityIcon/>
                </Link>
            },
        ],
        data: entitydata,
    };
    const handleUpdate = (newData) => {
        return Promise.resolve(console.log(newData));
    }
    return (

        <Grid item xs={12}>
            { role === 'Parent Organization' ?
            <div style={{maxWidth: "100%"}}>
                <MaterialTable
                    parentChildData={(row, rows) => rows.find(a => a.id === row.parentId)}
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
                        // grouping: true
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
            </div> : '' }

            {
                role === 'Child Entity' ?
                    <EntityDetailedPage entityid={oktaprofile.organization} /> : ''
            }



            {
              role === 'Administrator' ?
                  <div style={{maxWidth: "100%"}}>
                      <MaterialTable
                          parentChildData={(row, rows) => rows.find(a => a.id === row.parentId)}
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
                              // grouping: true
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


        </Grid>

    )
}


export default withRouter(EntityListing);
