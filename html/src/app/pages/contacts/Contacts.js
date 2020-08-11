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
import GeneralListing from "./GeneralListing";


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
    const [listcontacts, setListcontacts] = React.useState([])
    const [loading, setLoading] = React.useState(true)
    useEffect(() => {
        fetchContactData();
    }, []);

    const fetchContactData = async () => {
        try {
            const response = await contactList(oktaprofile.organization);
            await setListcontacts(response.data.contacts);
            setLoading(false);

        } catch (e) {
            console.log(e);
        }
    }
    const dummyDataa = {
        columns: [
            {title: 'Name', field: 'name'},
            {title: 'email', field: 'email'},
            {title: 'Phone', field: 'phone'},
        ],
        data: listcontacts,


    };

    return (
        <>
            <Title title={'Contacts'}/>
            <Grid container spacing={1}>
                <Grid item xs={12}>
                    <Portlet fluidHeight={true}>
                        <PortletHeader icon={<ContactPhoneIcon className={classes.adjustment}/>} title="Contact List"/>
                        <PortletBody>
                            <GeneralListing loading={loading} tooltip={'Add Contact'} redirect={true} data={dummyDataa}
                                            title={''}/>
                        </PortletBody>
                    </Portlet>
                </Grid>
            </Grid>
        </>
    )
}

export default Contacts;
