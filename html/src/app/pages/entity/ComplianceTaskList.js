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
import {entityList, entityListingAxios, taskUpdate} from "../../crud/enitity.crud";
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

function ComplianceTaskList(props) {
    const {oktaprofile, isAdmin} = useContext(OktaUserContext);
    const [loading, setLoading] = React.useState(false);
    const [data, setData] = React.useState(props.data)
    const history = useHistory();
    const classes = useStyles();

    // const [state, setState] = React.useState({
    //     columns: [
    //         {title: 'id', field: 'id'},
    //         {title: 'Name', field: 'subject'},
    //         {title: 'Due Date', field: 'dueDate'},
    //         {title: 'Status', field: 'status'},
    //     ],
    //     data: props.taskList,
    // });


    return (

        <Grid item xs={12}>
            <MaterialTable
                isLoading={loading ? loading : props.loading}
                actions={[
                    {
                        icon: 'check',
                        tooltip: 'Mark this task as complete',
                        position: 'row',
                        onClick: (event, rowData) => {
                            new Promise(resolve => {
                                setTimeout(() => {
                                    setLoading(true);
                                    let formData = new FormData();
                                    formData.append('status', 1);
                                    taskUpdate(rowData.id, formData);
                                    resolve();
                                    setLoading(false);
                                    history.go();
                                }, 600);
                            })
                        }
                    }

                ]}
                title={props.title !== '' ? props.title : ''}
                options={{
                    selection: props.selection ? props.selection : false,
                    actionsColumnIndex: -1
                }}
                columns={props.taskList.columns}
                data={props.taskList.data}
                // editable={{
                //     onRowUpdate: (newData, oldData) =>
                //         new Promise((resolve, reject) => {
                //             setTimeout(() => {
                //                 resolve();
                //             }, 1000);
                //         }),
                // }}

            />
        </Grid>

    )
}


export default withRouter(ComplianceTaskList);
