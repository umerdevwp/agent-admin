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
import {amber, green} from "@material-ui/core/colors";
import CheckCircleIcon from "@material-ui/icons/CheckCircle";
import WarningIcon from "@material-ui/icons/Warning";
import ErrorIcon from "@material-ui/icons/Error";
import InfoIcon from "@material-ui/icons/Info";
import SnackbarContent from "@material-ui/core/SnackbarContent";
import clsx from "clsx";
import IconButton from "@material-ui/core/IconButton";
import CloseIcon from "@material-ui/icons/Close";
import PropTypes from "prop-types";
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
    },

    loader: {
        marginTop: 7,
    },
    success: {
        backgroundColor: green[600],
    },
    error: {
        backgroundColor: theme.palette.error.dark,
    },
    info: {
        backgroundColor: theme.palette.primary.main,
    },
    warning: {
        backgroundColor: amber[700],
    },
    icon: {
        fontSize: 20,
    },
    iconVariant: {
        opacity: 0.9,
        marginRight: theme.spacing(1),
    },
    message: {
        display: 'flex',
        alignItems: 'center',
    },


}));




const variantIcon = {
    success: CheckCircleIcon,
    warning: WarningIcon,
    error: ErrorIcon,
    info: InfoIcon,
};


function MySnackbarContentWrapper(props) {
    const classes = useStyles();
    const {className, message, onClose, variant, ...other} = props;
    const Icon = variantIcon[variant];

    return (
        <SnackbarContent

            elevation={6} variant="filled"
            className={clsx(classes[variant], className)}
            aria-describedby="client-snackbar"
            message={
                <span id="client-snackbar" className={classes.message}>
          <Icon className={clsx(classes.icon, classes.iconVariant)}/>
                    {message}
        </span>
            }
            action={[
                <IconButton key="close" aria-label="Close" color="inherit" onClick={onClose}>
                    <CloseIcon className={classes.icon}/>
                </IconButton>,
            ]}
            {...other}
        />
    );
}

MySnackbarContentWrapper.propTypes = {
    className: PropTypes.string,
    message: PropTypes.node,
    onClose: PropTypes.func,
    variant: PropTypes.oneOf(['success', 'warning', 'error', 'info']).isRequired,
}
const RegisteredAgents = () => {
    const {oktaprofile, isAdmin} = useContext(OktaUserContext);
    const history = useHistory();
    const classes = useStyles();
    const [aAgents, setAAgents] = React.useState([])
    const [loading, setLoading] = React.useState(true);
    const [errors, setErrors] = React.useState({
        list: {

        }
    });
    useEffect(() => {
        fetchRaData();
    }, [])

    const fetchRaData = async () => {
        try {
            const response = await raList(oktaprofile.organization);
            if(response.type !== 'error') {
                setAAgents(response.data.aAgents);
                setLoading(false);
            } else {
                setErrors({...errors, list:response.message});
            }
        } catch (e){
            setErrors({...errors, list:e});
        }
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

    console.log(errors);

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
