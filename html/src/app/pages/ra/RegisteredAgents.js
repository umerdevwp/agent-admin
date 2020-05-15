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
import PictureAsPdfIcon from '@material-ui/icons/PictureAsPdf';
import MaterialTable from 'material-table';
import Autocomplete from "mui-autocomplete";
import TextField from '@material-ui/core/TextField';
import {OktaUserContext} from "../../context/OktaUserContext";
import {raList} from "../../crud/ra.crud";
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


const RegisteredAgents = () => {
    const {oktaprofile, isAdmin} = useContext(OktaUserContext);
    const history = useHistory();
    const classes = useStyles();
    const [aAgents, setAAgents] = React.useState([])
    const [loading, setLoading] = React.useState(true)
    useEffect(() => {
        fetchRaData();
    }, [])

    const fetchRaData = async () => {
        const response = await raList(oktaprofile.organization);
        setAAgents(response.data.aAgents);
        setLoading(false);
    }
    const dummyData = {
        columns: [
            {title: 'File As', field: 'fileAs' },
            {title: 'Address', field: 'address'},
            {title: 'Address 2', field: 'address2'},
            {title: 'State', field: 'state', editable: 'never'},
            {title: 'City', field: 'city', editable: 'never'},
            {title: 'Zipcode', field: 'zipcode', editable: 'never'},
        ],
        data: aAgents,
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
                            <ContactList loading={loading} tooltip={'Add new Registered Address'} redirect={true}
                                         url={'/dashboard/contact/form/add'} data={dummyData} title={''}/>
                        </PortletBody>
                    </Portlet>
                </Grid>
            </Grid>
        </>
    )
}

export default RegisteredAgents;
