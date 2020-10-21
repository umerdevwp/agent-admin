import React, {useContext, useEffect} from 'react';
import Grid from "@material-ui/core/Grid";

// import AttachmentIcon from '@material-ui/icons/Attachment';
import {makeStyles} from "@material-ui/core/styles";

import {useHistory} from "react-router-dom";

import PictureAsPdfIcon from '@material-ui/icons/PictureAsPdf';
import {withOktaAuth} from '@okta/okta-react';

import {UserContext} from "../context/UserContext";
import {AttachmentsList} from "../api/attachment";
import AttachmentTable from "../attachment/AttachmentTable";
import Layout from "../layout/Layout";
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

    errorMessage: {
        marginBottom: '5px'
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



const Attachments = () => {
    const {loading, attributes, addError, errorList, addTitle} = useContext(UserContext);

    // const history = useHistory();
    const classes = useStyles();
    const [attachmentList, setAttachmentList] = React.useState([])
    const [componentLoading, setComponentLoading] = React.useState(true);
    useEffect(() => {
        if (loading === true) {
            addTitle('Attachments');
            fetchAttachmentData();
        }
    }, [loading]);

    const fetchAttachmentData = async () => {
        try {
            const response = await AttachmentsList(attributes.organization);
            await setAttachmentList(response.data.documents);
            setComponentLoading(false);

        } catch (e) {
            addError('Something went wrong with the Attachment API.');
        }
    }







    const dummyData = {
        columns: [
            {
                title: 'File Name',
                editable: 'never',
                field: 'name',

            },
            {title: 'Date', field: 'created'},
            {title: 'Size', field: 'fileSize'},
        ],
        data: attachmentList,
    };


    return (
        <>
            <Layout>
                {errorList?.map((value, index) => (
                    <MySnackbarContentWrapper className={classes.errorMessage} spacing={1} index={index} variant="error"
                                              message={value}/>
                ))}
                <Grid container spacing={1}>
                    <Grid item xs={12}>
                        <AttachmentTable loading={componentLoading} tooltip={'Add Attachment'} data={dummyData}
                                     title={'Attachments'}/>
                    </Grid>
                </Grid>
            </Layout>
        </>
    )
}

export default withOktaAuth(Attachments);
