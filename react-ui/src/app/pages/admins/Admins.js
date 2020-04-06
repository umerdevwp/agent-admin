import React from 'react';
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

    const dummyData = {
        columns: [
            {title: 'First Name', field: 'first_name'},
            {title: 'Last Name', field: 'last_name'},
            {title: 'email', field: 'email'},
            {title: 'Last Activity', field: 'last_activity', editable: 'never'},
        ],
        data: [
            {id: 1, first_name: 'David', last_name: 'Baran', email: 'omer@gmail.com', last_activity: '2020-12-01'},
            {id: 2, first_name: 'David', last_name: 'Baran', email: 'omer@gmail.com', last_activity: '2020-12-01'},
            {id: 3, first_name: 'David', last_name: 'Baran', email: 'omer@gmail.com', last_activity: '2020-12-01'},
            {id: 4, first_name: 'David', last_name: 'Baran', email: 'omer@gmail.com', last_activity: '2020-12-01'},
            {id: 5, first_name: 'David', last_name: 'Baran', email: 'omer@gmail.com', last_activity: '2020-12-01'},
            {id: 6, first_name: 'David', last_name: 'Baran', email: 'omer@gmail.com', last_activity: '2020-12-01'},
            {id: 7, first_name: 'David', last_name: 'Baran', email: 'omer@gmail.com', last_activity: '2020-12-01'},
            {id: 8, first_name: 'David', last_name: 'Baran', email: 'omer@gmail.com', last_activity: '2020-12-01'},
            {id: 9, first_name: 'David', last_name: 'Baran', email: 'omer@gmail.com', last_activity: '2020-12-01'},
            {id: 10, first_name: 'David', last_name: 'Baran', email: 'omer@gmail.com', last_activity: '2020-12-01'},
            {id: 11, first_name: 'David Last', last_name: 'Baran', email: 'omer@gmail.com', last_activity: '2020-12-01'},

        ],
    };

    return (
        <>
            <Title title={'Administrator'}/>
            <Grid container spacing={1}>
                <Grid item xs={12}>
                    <Portlet fluidHeight={true}>
                        <PortletHeader icon={<AttachmentIcon className={classes.adjustment}/>} title="Admin List"/>
                        <PortletBody>
                            <EntityListing data={dummyData} title={''}/>
                        </PortletBody>
                    </Portlet>
                </Grid>
            </Grid>
        </>
    )
}

export default Admins;
