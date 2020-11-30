import React, {useContext, useEffect} from 'react';
import {makeStyles} from '@material-ui/core/styles';
import Typography from '@material-ui/core/Typography';

import clsx from 'clsx';
import FormGroup from '@material-ui/core/FormGroup';
import CircularProgress from '@material-ui/core/CircularProgress';
import {withOktaAuth} from '@okta/okta-react';
import Backdrop from '@material-ui/core/Backdrop';
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
import {EntityList, lorexFileUpload} from "../api/enitity.crud";
import {attachFiles} from "../api/attachment";
import Breadcrumbs from "@material-ui/core/Breadcrumbs";
import Link from "@material-ui/core/Link";
import Paper from "@material-ui/core/Paper";
import Layout from "../layout/Layout";
import {UserContext} from "../context/UserContext";
import {DropzoneArea} from 'material-ui-dropzone';
import {AttachFile, Description, PictureAsPdf, Theaters} from '@material-ui/icons';
import Select from "@material-ui/core/Select";
import FormControl from "@material-ui/core/FormControl";
import InputLabel from "@material-ui/core/InputLabel";
import FormHelperText from "@material-ui/core/FormHelperText";
import Box from '@material-ui/core/Box';
import Autocomplete from '@material-ui/lab/Autocomplete';
import TextField from '@material-ui/core/TextField';

const handlePreviewIcon = (fileObject, classes) => {
    const {type} = fileObject.file
    const iconProps = {
        className: classes.image,
    }

    if (type.startsWith("video/")) return <Theaters {...iconProps} />


    switch (type) {
        case "application/msword":
        case "application/vnd.openxmlformats-officedocument.wordprocessingml.document":
            return <Description {...iconProps} />
        case "application/pdf":
            return <PictureAsPdf {...iconProps} />
        default:
            return <AttachFile {...iconProps} />
    }
}
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
        paddingLeft: 10,
        paddingRight: 10
    },
    breadcrumbsDiv: {
        marginBottom: 30
    },
    breadcrumbsInner: {
        padding: 10
    },

    baseColor: {
        color: '#48465b'
    },

    backdrop: {
        zIndex: theme.zIndex.drawer + 1,
        color: '#fff',
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

const AdminAddAttachmentForm = (props) => {

    const {loading, addTitle, addError, role} = useContext(UserContext);
    const classes = useStyles();
    const history = useHistory();


    const [apiLoading, setApiLoading] = React.useState(false);
    // const [error, setError] = React.useState(false);
    const [inputFileName, setInputFileName] = React.useState({value: '', error: ' '});
    const [inputFiling, setInputFiling] = React.useState({value: '', error: ' ', success: ' '});
    const [inputFileSize, setInputFileSize] = React.useState({value: '', error: ' ', success: ' '});
    const [successMessage, setSuccessMessage] = React.useState(' ');
    const [errorMessage, setErrorMessage] = React.useState(' ');
    const [files, setFiles] = React.useState([]);
    const [fileProgress, setFileProgress] = React.useState(0);
    const [state, setState] = React.useState({counter: 0});
    const [entityId, setEntityId] = React.useState({value: '', error: ' '});
    const [entityList, setEntityList] = React.useState([]);
    const [key, setKey] = React.useState(0);
    var fileIndex = 0;
    useEffect(() => {
        if (loading === true) {
            addTitle('Bulk Attachments');
            entitylisitingLoader();
        }
    }, [loading]);


    useEffect(() => {
        if (state.counter === files.length) {
            if (files.length > 0) {
                setSuccessMessage('Uploaded Successfully');
                setApiLoading(false);
                setState({counter: 0});
                reset();
            }

        }
    }, [state]);

    const reset = () => {
        setFiles([]);
        fileIndex = 0;
        setKey(Math.floor((Math.random() * 10) + 1));
    }
    const entitylisitingLoader = async () => {
        setApiLoading(true);
        var EntityListLocal = await JSON.parse(localStorage.getItem("EntityList") !== 'undefined' ? localStorage.getItem("EntityList") : '');
        if (EntityListLocal !== 'undefined' && EntityListLocal) {
            setEntityList(EntityListLocal);
            setApiLoading(false);
        } else {
            const response = await EntityList();

            if (response.error) {
                addError(response.error.message);
            }

            if (response.type === 'error') {
                window.location.reload();
            }

            if (response.status === 401) {
                window.location.reload();
            }


            localStorage.setItem("EntityList", JSON.stringify(response.data));
            await setEntityList(response.data);
            setApiLoading(false);
        }
    }

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


    const handleChange = (files) => {
        setFiles(files);
    }


    const handleOnSubmit = async (event) => {
        event.preventDefault();
        setApiLoading(true);

        if (files.length === 0) {
            setErrorMessage('File is required');
            setApiLoading(false);
        } else {
            setErrorMessage(' ');
        }

        if (entityId.value === '') {
            setEntityId({...entityId, error: 'Entity field is required'});
            setApiLoading(false);
        } else {
            setEntityId({...entityId, error: ' '})
        }

        files.map(async (anObjectMapped, index) => {
            let formData = new FormData();
            formData.append('file', anObjectMapped);
            const response = await lorexFileUpload(formData);
            if (response.error === false) {
                fileIndex = fileIndex + 1;


                formData.append('entityId', entityId.value);
                formData.append('inputFileId', response.record_id);
                formData.append('inputFileName', anObjectMapped.name);
                formData.append('inputFileSize', response.file_size);
                formData.append('bulkUpload', fileIndex);
                formData.append('totalBulkUpload', files.length);

                try {
                    const response = await attachFiles(formData);
                    if (response) {
                        if (response.status === true) {
                            setState((prevState, props) => {
                                return {counter: prevState.counter + 1};
                            });
                        }
                    }
                } catch (e) {
                    addError('Something went wrong with Attchemnt API.');
                    setErrorMessage('Something went wrong with Attchemnt API.');
                }
            }

            if (response.error === true) {
                addError(response.message);
            }

        })


        // setLoading(true);
        // let formData = new FormData();
        // console.log(files);
// console.log(inputFormationDate.value);

// Display the key/value pairs
//         if (attributes.organization) {
//             formData.append('entityId', props.match.params.id ? props.match.params.id : attributes.organization);
//         }
//         formData.append('inputFileId', inputFiling.value);
//         formData.append('inputFileName', inputFileName.value);
//         formData.append('inputFileSize', inputFileSize.value);

        // try {
        //     const response = await attachFiles(formData);
        //     if (response) {
        //         if (response.status === true) {
        //             setSuccessMessage(response.message);
        //             setLoading(false);
        //         }
        //     }
        // } catch (e) {
        //     addError('Something went wrong with Attchemnt API.');
        //     setErrorMessage('Something went wrong with Attchemnt API.');
        // }

        // if(response.status == true) {
        //     setTimeout(() => {
        //         history.goBack();
        //     }, 4000)
        // }

    }

    const fileChange = async (e) => {
        setApiLoading(true);
        let formData = new FormData();
        formData.append('file', e.target.files[0]);
        const filename = e.target.files[0].name;
        const response = await lorexFileUpload(formData);
        if (response.error === false) {
            setInputFiling({...inputFiling, value: response.record_id, success: 'uploaded'});
            setInputFileSize({...inputFileSize, value: response.file_size})
            if (filename) {
                setInputFileName({...inputFileName, value: filename});
                setApiLoading(false);
            }
        } else {
            setApiLoading(false);
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
            {role === 'Administrator' ?
                <>
                    {history.length > 0 ?
                        <div className={classes.breadcrumbsDiv}>
                            <Paper className={classes.breadcrumbsInner} elevation={1}>
                                <Typography className={classes.baseColor} color="inherit"
                                            variant="h4">Navigation</Typography>
                                <Breadcrumbs aria-label="breadcrumb">

                                    {/*<Link color="inherit" href="/">*/}
                                    {/*    <Typography color="textPrimary">Dashboard</Typography>*/}
                                    {/*</Link>*/}

                                    <Link color="inherit" onClick={(e) => {
                                        history.goBack()
                                    }}>
                                        <Typography color="textPrimary">Attachments</Typography>
                                    </Link>
                                    <Typography color="textPrimary">Add Attachment</Typography>
                                </Breadcrumbs>
                            </Paper>
                        </div>
                        : ''}

                    <Paper className={classes.paper} elevation={3}>
                        {successMessage !== ' ' ? (
                            <MySnackbarContentWrapper
                                onClose={() => {
                                    removeSuccess()
                                }}
                                variant="success"
                                message={successMessage}
                            />
                        ) : ''}


                        {errorMessage !== ' ' ? (
                            <MySnackbarContentWrapper
                                onClose={() => {
                                    removeErrorMessage()
                                }}
                                variant="error"
                                message={errorMessage}
                            />
                        ) : ''}
                        {/*<Backdrop className={classes.backdrop} open={apiLoading}>*/}
                        {/*    <CircularProgress color="inherit" />*/}
                        {/*</Backdrop>*/}
                        <div className="row">
                            <form className={classes.formStyle} onSubmit={handleOnSubmit} noValidate
                                  autoComplete="off">


                                <div className={'col-md-6'}>
                                    <FormControl className={clsx(classes.selectField)}
                                                 error={entityId.error !== ' '}>
                                <Autocomplete
                                    onChange={(event, newValue) => {
                                       if(newValue) {
                                           setEntityId({
                                               ...entityId,
                                               value: newValue.id
                                           })
                                       } else {
                                           setEntityId({
                                               ...entityId,
                                               value: ''
                                           })
                                       }
                                    }}

                                    id="combo-box-demo"
                                    options={entityList ? entityList : ''}
                                    getOptionLabel={(option) => option.account_name}
                                    renderInput={(params) => <TextField error={entityId.error !== ' '} {...params} label="Entity Name" variant="outlined" />}
                                />
                                        <FormHelperText>{entityId.error}</FormHelperText>
                                    </FormControl>
                                </div>

                                <FormGroup row>

                                    <div className="col-md-12">
                                        <DropzoneArea key={key} getPreviewIcon={handlePreviewIcon} filesLimit={50}
                                                      showPreviews={true}
                                                      showPreviewsInDropzone={false}
                                                      useChipsForPreview
                                                      previewGridProps={{container: {spacing: 1, direction: 'row'}}}
                                                      previewChipProps={{classes: {root: classes.previewChip}}}
                                                      previewText="Selected files"
                                                      acceptedFiles={['.pdf']}
                                                      onChange={handleChange}
                                                      disableRejectionFeedback={apiLoading}
                                                      clearOnUnmount={true}
                                                      getFileAddedMessage={(fileName) => `File ${fileName} ready to add document.`}

                                        />
                                    </div>
                                    {/*<div className={'col-md-6'}>*/}
                                    {/*    <CustomFileInput*/}
                                    {/*        disabled={loading}*/}
                                    {/*        required*/}
                                    {/*        id="attachment"*/}
                                    {/*        value={inputFiling.value.File}*/}
                                    {/*        onChange={e => fileChange(e)}*/}
                                    {/*        label="Attachment"*/}
                                    {/*        className={clsx(classes.fileUploading, classes.dense)}*/}
                                    {/*        margin="dense"*/}
                                    {/*        invalid={inputFiling.error !== ' '}*/}
                                    {/*        valid={inputFiling.success !== ' '}*/}
                                    {/*    />*/}
                                    {/*    <span>{inputFiling.success !== ' ' ? inputFiling.success : ' '}</span>*/}
                                    {/*</div>*/}

                                    <div className={'col-md-12'}>

                                        <div className={clsx(classes.submitButton, 'custom-button-wrapper')}>
                                            {apiLoading ? (
                                                files.length !== 0 ?
                                                    <Box position="relative" display="inline-flex">
                                                        <CircularProgress variant="static"
                                                                          value={Math.round(state.counter / files.length * 100)}/>
                                                        <Box
                                                            top={0}
                                                            left={0}
                                                            bottom={0}
                                                            right={0}
                                                            position="absolute"
                                                            display="flex"
                                                            alignItems="center"
                                                            justifyContent="center"
                                                        >
                                                            <Typography variant="caption" component="div"
                                                                        color="textSecondary">{Math.round(state.counter / files.length * 100)}%</Typography>
                                                        </Box>
                                                    </Box> : ''

                                            ) : null}

                                            {/*{apiLoading ? (*/}
                                            {/*        <div className={clsx(classes.loader)}>*/}
                                            {/*            <FacebookProgress/>*/}
                                            {/*        </div>)*/}
                                            {/*    : null}*/}

                                            <input disabled={apiLoading}
                                                   className={clsx('btn btn-primary', classes.restButton)}
                                                   type="submit" value="Add attachment"/>

                                        </div>
                                    </div>
                                </FormGroup>
                            </form>
                        </div>

                    </Paper>
                </> : <MySnackbarContentWrapper
                    variant="error"
                    message={'Access Denied.'}
                />}
        </Layout>
    )
}


export default withOktaAuth(AdminAddAttachmentForm);
