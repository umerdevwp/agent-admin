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
import PictureAsPdfIcon from '@material-ui/icons/PictureAsPdf';
import MaterialTable from 'material-table';
import Autocomplete from "mui-autocomplete";
import TextField from '@material-ui/core/TextField';


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


const RegisteredAgents = () => {
    const history = useHistory();
    const classes = useStyles();

    const dummyData = {
        columns: [
            {title: 'File As', field: 'file_as' },
            {title: 'Address', field: 'address'},
            {title: 'Address 2', field: 'address2'},
            {title: 'State', field: 'state', editable: 'never'},
            {title: 'City', field: 'city', editable: 'never'},
            {title: 'Zipcode', field: 'zipcode', editable: 'never'},
        ],
        data: [
            {id: 1, file_as: 'United Agent Services LLC', address: '100 Oxmoor Road', address2: 'Suite 110', state: 'AL', city: 'Birmingham', zipcode: '35209'},
            {id: 2, file_as: '700 12th Street', address: '100 Oxmoor Road', address2: 'Suite 110', state: 'AL', city: 'Birmingham', zipcode: '35209'},
            {id: 3, file_as: '5575 S. Semoran Blvd', address: '100 Oxmoor Road', address2: 'Suite 110', state: 'AL', city: 'Birmingham', zipcode: '35209'},
            {id: 4, file_as: 'United Agent Services LLC', address: '100 Oxmoor Road', address2: 'Suite 110', state: 'AL', city: 'Birmingham', zipcode: '35209'},
            {id: 5, file_as: '1003 Bishop St', address: '100 Oxmoor Road', address2: 'Suite 110', state: 'AL', city: 'Birmingham', zipcode: '35209'},
            {id: 6, file_as: '1420 Southlake Plaza Dr', address: '100 Oxmoor Road', address2: 'Suite 110', state: 'AL', city: 'Birmingham', zipcode: '35209'},
            {id: 7, file_as: 'United Agent Services LLC', address: '100 Oxmoor Road', address2: 'Suite 110', state: 'AL', city: 'Birmingham', zipcode: '35209'},
        ],
    };
    const Additem = (event) => {
        console.log('Lorem');
    }



    return (
        <>
            <Title title={'Registered Agents'}/>
            <Grid container spacing={1}>
                <Grid item xs={12}>
                    <Portlet fluidHeight={true}>
                        <PortletHeader icon={<AttachmentIcon className={classes.adjustment}/>} title="List of Registered Agents"/>
                        <PortletBody>
                            <EntityListing tooltip={'Add Attachment'} redirect={true} url={'/dashboard/attachment/form/add'} click={Additem} data={dummyData} title={''}/>
                        </PortletBody>
                    </Portlet>
                </Grid>
            </Grid>
        </>
    )
}

export default RegisteredAgents;
