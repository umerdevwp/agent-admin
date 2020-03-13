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


const Attachments = () => {
    const history = useHistory();
    const classes = useStyles();

    const dummyData = {
        columns: [
            {
                title: 'File Name',
                editable: 'never',
                render: rowData => rowData ? (<Link
                    component="button"
                    variant="body2"
                    onClick={() => {
                        history.push(`/dashboard/entity/${rowData.id}`);
                    }}>
                    <PictureAsPdfIcon/> {rowData.file_name}
                </Link>) : 'Sample.pdf'
            },
            {title: 'Attached By', field: 'attachment_by'},
            {title: 'Date Added', field: 'date_added'},
            {title: 'Size', field: 'size', editable: 'never'},
        ],
        data: [
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},


        ],
    };


    const Additem = (event) => {
        console.log('lulu');
    }


    return (
        <>
            <Title title={'Attachments'}/>
            <Grid container spacing={1}>
                <Grid item xs={12}>
                    <Portlet fluidHeight={true}>
                        <PortletHeader icon={<AttachmentIcon className={classes.adjustment}/>} title="Attachment List"/>
                        <PortletBody>
                            <EntityListing tooltip={'Add Attachment'} redirect={true} url={'/dashboard/attachment/form/add'} click={Additem} data={dummyData} title={''}/>
                        </PortletBody>
                    </Portlet>
                </Grid>
            </Grid>
        </>
    )
}

export default Attachments;
