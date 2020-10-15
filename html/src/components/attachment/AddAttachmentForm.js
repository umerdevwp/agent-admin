import React, {useContext} from 'react';
import {makeStyles} from '@material-ui/core/styles';
import Typography from '@material-ui/core/Typography';
import Grid from "@material-ui/core/Grid";
import PermIdentityIcon from '@material-ui/icons/PermIdentity';
import clsx from 'clsx';
import FormGroup from '@material-ui/core/FormGroup';
import CustomFileInput from "reactstrap/es/CustomFileInput";
import CircularProgress from '@material-ui/core/CircularProgress';
import {withOktaAuth} from '@okta/okta-react';

import PropTypes from 'prop-types';
import ErrorIcon from '@material-ui/icons/Error';
import InfoIcon from '@material-ui/icons/Info';
import CloseIcon from '@material-ui/icons/Close';
import {amber, green} from '@material-ui/core/colors';
import IconButton from '@material-ui/core/IconButton';
import Snackbar from '@material-ui/core/Snackbar';
import SnackbarContent from '@material-ui/core/SnackbarContent';
import WarningIcon from '@material-ui/icons/Warning';
import CheckCircleIcon from '@material-ui/icons/CheckCircle';
import {useHistory} from "react-router-dom";
import {lorexFileUpload} from "../api/enitity.crud";
import {attachFiles} from "../api/attachment";
import Breadcrumbs from "@material-ui/core/Breadcrumbs";
import Link from "@material-ui/core/Link";
import Paper from "@material-ui/core/Paper";
import Layout from "../layout/Layout";
import {UserContext} from "../context/UserContext";

const useStylesFacebook = makeStyles({
    root: {
        position: 'relative',
    },
    top: {
        color: '#eef3fd',
    },
    bottom: {
        color: '#6798e5',
        animationDuration: '550ms',
        position: 'absolute',
        left: 0,
    },
});

const useStyles = makeStyles(theme => ({
    root: {
        flexGrow: 1,
    },
    backButton: {
        marginRight: theme.spacing(1),
    },
    instructions: {
        marginTop: theme.spacing(1),
        marginBottom: theme.spacing(1),
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
    textField: {
// marginLeft: theme.spacing(1),
// marginRight: theme.spacing(1),
        width: '100%',
    },

    textFieldOther: {
        width: '100%',
    },

    textFieldCity: {

        marginTop: 3,
    },


    textFieldtwofield: {
// marginLeft: theme.spacing(1),
// marginRight: theme.spacing(1),
        width: '100%',
    },


    selectField: {
// marginLeft: theme.spacing(1),
// marginRight: theme.spacing(1),
        width: '100%',
        marginTop: 16,
    },
    dense: {
        marginTop: 16,
    },
    container: {
        display: 'flex',
        flexWrap: 'wrap',
    },
    checkbox: {
        marginTop: 30
    },

    fileUploading: {
        zIndex: 0,
        marginTop: 22,
        width: '100%',
    },

    submitButton: {
        marginTop: 15,
        float: 'right',
        display: 'inline-flex'
    },

    restButton: {

        marginLeft: 20,
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

    formStyle: {
        width: '100%'
    },
    paper: {
        paddingTop: 30,
        paddingBottom: 30,
        paddingLeft:10,
        paddingRight:10
    },
    breadcrumbsDiv: {
        marginBottom: 30
    },
    breadcrumbsInner: {
        padding: 10
    },

    baseColor: {
        color: '#48465b'
    }



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

const AddAttachmentForm = (props) => {

    const {addTitle, addError, attributes} = useContext(UserContext);
    addTitle('Add New Attachment');
    const classes = useStyles();
    const history = useHistory();

    const [loading, setLoading] = React.useState(false)
    // const [error, setError] = React.useState(false);
    const [inputFileName, setInputFileName] = React.useState({value: '', error: ' '});
    const [inputFiling, setInputFiling] = React.useState({value: '', error: ' ', success: ' '});
    const [inputFileSize, setInputFileSize] = React.useState({value: '', error: ' ', success: ' '});
    const [successMessage, setSuccessMessage] = React.useState(' ');
    const [errorMessage, setErrorMessage] = React.useState(' ');

    function FacebookProgress(props) {
        const classes = useStylesFacebook();

        return (
            <div className={classes.root}>
                <CircularProgress

                    variant="determinate"
                    value={100}
                    className={classes.top}
                    size={24}
                    thickness={4}
                    {...props}
                />
                <CircularProgress
                    variant="indeterminate"
                    disableShrink
                    className={classes.bottom}
                    size={24}
                    thickness={4}
                    {...props}
                />
            </div>
        );
    }


    // const handleClose = (event, reason) => {
    //     setError(false);
    // }

    const handleOnSubmit = async (event) => {
        event.preventDefault();
        setLoading(true);
        let formData = new FormData();

// console.log(inputFormationDate.value);

// Display the key/value pairs
        if(attributes.organization) {
            formData.append('entityId', props.match.params.id ? props.match.params.id : attributes.organization);
        }
        formData.append('inputFileId', inputFiling.value);
        formData.append('inputFileName', inputFileName.value);
        formData.append('inputFileSize', inputFileSize.value);

        try {
            const response = await attachFiles(formData);
            if (response) {
                if (response.status === true) {
                    setSuccessMessage(response.message);
                    setLoading(false);
                }
            }
        } catch (e) {
            addError('Something went wrong with Attchemnt API.');
            setErrorMessage('Something went wrong with Attchemnt API.');
        }

        // if(response.status == true) {
        //     setTimeout(() => {
        //         history.goBack();
        //     }, 4000)
        // }

    }

    const fileChange = async (e) => {
        setLoading(true);
        let formData = new FormData();
        formData.append('file', e.target.files[0]);
        const filename = e.target.files[0].name;
        const response = await lorexFileUpload(formData);
        if (response.error === false) {
            setInputFiling({...inputFiling, value: response.record_id, success: 'uploaded'});
            setInputFileSize({...inputFileSize, value: response.file_size})
            if (filename) {
                setInputFileName({...inputFileName, value: filename});
                setLoading(false);
            }
        } else {
            setLoading(false);
        }
    }

    const removeSuccess = () => {
        setSuccessMessage(' ');
    }

    const removeErrorMessage = () => {
        setErrorMessage(' ');
    }


    return (

        <Layout>
            <div className={classes.breadcrumbsDiv}>
                <Paper className={classes.breadcrumbsInner} elevation={1}>
                    <Typography className={classes.baseColor} color="inherit" variant="h4">Navigation</Typography>
                    <Breadcrumbs aria-label="breadcrumb">

                        <Link color="inherit" href="/">
                            <Typography color="textPrimary">Dashboard</Typography>
                        </Link>

                        <Link color="inherit" onClick={(e) => {
                            history.goBack()
                        }}>
                            <Typography color="textPrimary">Entity</Typography>
                        </Link>
                        <Typography color="textPrimary">Add Attachment</Typography>
                    </Breadcrumbs>
                </Paper>
            </div>

            <Paper className={classes.paper} elevation={3}>
                {successMessage !== ' ' ? (
                    <MySnackbarContentWrapper
                        onClose={()=>{removeSuccess()}}
                        variant="success"
                        message={successMessage}
                    />
                ) : ''}


                {errorMessage !== ' ' ? (
                    <MySnackbarContentWrapper
                        onClose={()=>{removeErrorMessage()}}
                        variant="error"
                        message={errorMessage}
                    />
                ) : ''}
                <div className="row">
                    <form className={classes.formStyle} onSubmit={handleOnSubmit} noValidate
                          autoComplete="off">
                        <FormGroup row>
                            <div className={'col-md-6'}>
                                <CustomFileInput
                                    disabled={loading}
                                    required
                                    id="attachment"
                                    value={inputFiling.value.File}
                                    onChange={e => fileChange(e)}
                                    label="Attachment"
                                    className={clsx(classes.fileUploading, classes.dense)}
                                    margin="dense"
                                    invalid={inputFiling.error !== ' '}
                                    valid={inputFiling.success !== ' '}
                                />
                                <span>{inputFiling.success !== ' ' ? inputFiling.success : ' '}</span>
                            </div>

                            <div className={'col-md-12'}>
                                <div className={clsx(classes.submitButton, 'custom-button-wrapper')}>
                                    {loading ? (
                                            <div className={clsx(classes.loader)}>
                                                <FacebookProgress/>
                                            </div>)
                                        : null}

                                    <input disabled={loading}
                                           className={clsx('btn btn-primary', classes.restButton)}
                                           type="submit" value="Add attachment"/>

                                </div>
                            </div>
                        </FormGroup>
                    </form>
                </div>

            </Paper>
        </Layout>
    )
}


export default withOktaAuth(AddAttachmentForm);
