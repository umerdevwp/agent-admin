import React, {useContext, useEffect} from 'react';
import MaterialTable from 'material-table';

import {makeStyles} from '@material-ui/core/styles';
import {
    withRouter
} from 'react-router-dom';
import {useHistory} from "react-router-dom";
import VisibilityIcon from '@material-ui/icons/Visibility';
import Grid from "@material-ui/core/Grid";
import {UserContext} from "../context/UserContext";



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

    const [loading, setLoading] = React.useState(false);
    const history = useHistory();

    return (

        <Grid item xs={12}>
            <MaterialTable
                isLoading={loading ? loading : props.loading}
                actions={  props.action ? [
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
                columns={props.data.columns}
                data={props.data.data}

            />
        </Grid>

    )
}
export default withRouter(ContactList);
