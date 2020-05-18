import React, {useContext, useEffect} from 'react';
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
import {OktaUserContext} from "../../context/OktaUserContext";
import {contactList} from "../../crud/contact.crud";
import ContactList from "../entity/ContactList";
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
    const {oktaprofile, isAdmin} = useContext(OktaUserContext);
    const history = useHistory();
    const classes = useStyles();
    const [contacts, setContacts] = React.useState([])
    const [loading, setLoading] = React.useState(true)
    useEffect(() => {
        fetchContactData();
    }, []);

    const fetchContactData = async () => {
        const response = await contactList(oktaprofile.organization);
        setContacts(response.data.contacts);
        setLoading(false);
    }
    const dummyData = {
        columns: [
            {title: 'Name', field: 'name'},
            {title: 'Contact Type', field: 'title'},
            {title: 'email', field: 'email'},
            {title: 'Street', field: 'mailing_street'},
            {title: 'City', field: 'mailing_city'},
            {title: 'State', field: 'mailing_state'},
            {title: 'Phone', field: 'phone'},
        ],
        data: contacts,


    };

    return (
        <>
            <Title title={'Contacts'}/>
            <Grid container spacing={1}>
                <Grid item xs={12}>
                    <Portlet fluidHeight={true}>
                        <PortletHeader icon={<ContactPhoneIcon className={classes.adjustment}/>} title="Contact List"/>
                        <PortletBody>
                            <ContactList loading={loading} tooltip={'Add Contact'} redirect={true}
                                         url={'/dashboard/contact/form/add'} data={dummyData} title={''}/>                        </PortletBody>
                    </Portlet>
                </Grid>
            </Grid>
        </>
    )
}

export default Contacts;
