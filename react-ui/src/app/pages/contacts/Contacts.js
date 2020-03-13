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
import ContactPhoneIcon from '@material-ui/icons/ContactPhone';


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



const Contacts = () => {
    const history = useHistory();
    const classes = useStyles();

    const dummyData = {
        columns: [
            {title: 'Name', field: 'full_name'},
            {title: 'Contact Type', field: 'title'},
            {title: 'email', field: 'email'},
            {title: 'Street', field: 'mailing_street'},
            {title: 'City', field: 'mailing_city'},
            {title: 'State', field: 'mailing_state'},
            {title: 'Phone', field: 'phone'},
        ],
        data: [
            {id: 1, full_name: 'David', title: 'CFO', email: 'omer@gmail.com', mailing_street: 'Abc Dr, Beloit WI', mailing_city: 'Beloit', mailing_state:'Wisconsin - WI', phone: '03335614017'},
            {id: 1, full_name: 'David Off', title: 'CFO', email: 'omer@gmail.com', mailing_street: 'Abc Dr, Beloit WI', mailing_city: 'Beloit', mailing_state:'Wisconsin - WI', phone: '03335614017'},
            {id: 1, full_name: 'David Test', title: 'CFO', email: 'omer@gmail.com', mailing_street: 'Abc Dr, Beloit WI', mailing_city: 'Beloit', mailing_state:'Wisconsin - WI', phone: '03335614017'},
            {id: 1, full_name: 'David', title: 'CFO', email: 'omer@gmail.com', mailing_street: 'Abc Dr, Beloit WI', mailing_city: 'Beloit', mailing_state:'Wisconsin - WI', phone: '03335614017'},
            {id: 1, full_name: 'David', title: 'CFO', email: 'omer@gmail.com', mailing_street: 'Abc Dr, Beloit WI', mailing_city: 'Beloit', mailing_state:'Wisconsin - WI', phone: '03335614017'},
            {id: 1, full_name: 'David', title: 'CEO', email: 'omer@gmail.com', mailing_street: 'Abc Dr, Beloit WI', mailing_city: 'Beloit', mailing_state:'Wisconsin - WI', phone: '03335614017'},
            {id: 1, full_name: 'David', title: 'CFO', email: 'omer@gmail.com', mailing_street: 'Abc Dr, Beloit WI', mailing_city: 'Beloit', mailing_state:'Wisconsin - WI', phone: '03335614017'},
            {id: 1, full_name: 'David', title: 'Officer', email: 'omer@gmail.com', mailing_street: 'Abc Dr, Beloit WI', mailing_city: 'Beloit', mailing_state:'Wisconsin - WI', phone: '03335614017'},
            {id: 1, full_name: 'David', title: 'CFO', email: 'omer@gmail.com', mailing_street: 'Abc Dr, Beloit WI', mailing_city: 'Beloit', mailing_state:'Wisconsin - WI', phone: '03335614017'},
            {id: 1, full_name: 'David', title: 'CFO', email: 'omer@gmail.com', mailing_street: 'Abc Dr, Beloit WI', mailing_city: 'Beloit', mailing_state:'Wisconsin - WI', phone: '03335614017'},


        ],


    };

    return (
        <>
            <Title title={'Contacts'}/>
            <Grid container spacing={1}>
                <Grid item xs={12}>
                    <Portlet fluidHeight={true}>
                        <PortletHeader icon={<ContactPhoneIcon className={classes.adjustment}/>} title="Contact List"/>
                        <PortletBody>
                            <EntityListing tooltip={'Add New Contact'} redirect={true} url={'/dashboard/contact/form/add'} data={dummyData} title={''}/>
                        </PortletBody>
                    </Portlet>
                </Grid>
            </Grid>
        </>
    )
}

export default Contacts;
