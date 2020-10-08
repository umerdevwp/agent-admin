import React, {useContext} from 'react';
import {makeStyles} from '@material-ui/core/styles';
import Button from '@material-ui/core/Button';
import Typography from '@material-ui/core/Typography';
import Container from "@material-ui/core/Container";
import Grid from "@material-ui/core/Grid";

import PermIdentityIcon from '@material-ui/icons/PermIdentity';
import clsx from 'clsx';
import TextField from '@material-ui/core/TextField';
import FormGroup from '@material-ui/core/FormGroup';
import FormControlLabel from '@material-ui/core/FormControlLabel';
import Switch from '@material-ui/core/Switch';
import Checkbox from '@material-ui/core/Checkbox';
import Select from '@material-ui/core/Select';
import InputLabel from '@material-ui/core/InputLabel';
import FormControl from '@material-ui/core/FormControl';

import Autocomplete from "../entity/TestAutocomplete";

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
import {ContactTypeList, EntitytypesList, StateRegionList} from "../api/enitity.crud";
import {createContact} from "../api/contact.crud";
import {UserContext} from "../context/UserContext";
import Breadcrumbs from "@material-ui/core/Breadcrumbs";
import Link from "@material-ui/core/Link";
import * as SmartyStreetsSDK from "smartystreets-javascript-sdk";
import utils from 'smartystreets-javascript-sdk-utils';
import Dialog from '@material-ui/core/Dialog';
import DialogActions from '@material-ui/core/DialogActions';
import DialogContent from '@material-ui/core/DialogContent';
import DialogContentText from '@material-ui/core/DialogContentText';
import DialogTitle from '@material-ui/core/DialogTitle';
import FormHelperText from "@material-ui/core/FormHelperText";
import Layout from "../layout/Layout";
import Paper from "@material-ui/core/Paper";


const Lookup = SmartyStreetsSDK.usStreet.Lookup;


const smartyStreetsSharedCredentials = new SmartyStreetsSDK.core.SharedCredentials(process.env.REACT_APP_SMARTYSTREET_KEY);
const clientBuilder = new SmartyStreetsSDK.core.ClientBuilder(smartyStreetsSharedCredentials);
let client = clientBuilder.buildUsStreetApiClient();


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

const AddContactForm = (props) => {

    const history = useHistory();
    const classes = useStyles();


    const {attributes, addTitle} = useContext(UserContext);
    addTitle('Add New Contact');

    const [addressObject, setAddressObject] = React.useState([]);
    const [addressValue, setAddressValue] = React.useState('');
    const [addressReset, setAddressReset] = React.useState('');
    const [loading, setLoading] = React.useState(false)
    const [contactType, setContactType] = React.useState([]);
    const [successMessage, setSuccessMessage] = React.useState(' ');
    const [StateRegion, setStateRegion] = React.useState([])
    //form state


    const [inputContactFirstName, setInputContactFirstName] = React.useState({value: '', error: ' '});
    const [inputContactLastName, setInputLastName] = React.useState({value: '', error: ' '});
    const [inputContactEmail, setInputNotificationEmail] = React.useState({value: '', error: ' '});
    const [inputContactPhone, setInputNotificationPhone] = React.useState({value: '', error: ' '});
    const [inputContactStreet, setInputNotificationAddress] = React.useState({value: '', error: ' '});
    const [inputContactType, setInputNotificationContactType] = React.useState({value: '', error: ' '});
    const [inputContactCity, setInputNotificationCity] = React.useState({value: '', error: ' '});
    const [inputContactState, setInputNotificationState] = React.useState({value: '', error: ' '});
    const [inputContactZipcode, setInputNotificationZip] = React.useState({value: '', error: ' '});
    const [open, setOpen] = React.useState(false);
    const [userAgree, setUserAgree] = React.useState(false);
    const [isValidAddress, setIsValidAddress] = React.useState(false);
    const [errorMessage, setErrorMessage] = React.useState(' ');
    const handleClickOpen = () => {
        setOpen(true);
    };

    const handleClose = () => {
        setLoading(false);
        setUserAgree(false);
        setOpen(false);
    };

    const iAgree = async (event) => {
        Promise.resolve(setTimeout(() => {
            setUserAgree(true);
        }, 3000));
        Promise.resolve(setOpen(false));
        handleOnSubmit(event, true)

    };

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


    React.useEffect(() => {
        if (addressObject) {
            if (typeof addressObject === 'object') {
                setInputNotificationCity({...inputContactCity, error: ' ', value: addressObject.city})
                setInputNotificationState({...inputContactState, error: ' ', value: addressObject.state})
            }
        }

    }, [addressObject, addressValue])


    React.useEffect(() => {
        fetchDataforDropdownsContactTypeList();
        fetchDataforDropdownsStateRegion();
        localStorage.setItem('iAgree', false);
    }, [])


    const fetchDataforDropdownsContactTypeList = async () => {
        var ContactTypeListLocal = await JSON.parse(localStorage.getItem("ContactTypeList"));
        if (ContactTypeListLocal) {
            setContactType(ContactTypeListLocal);
        } else {
            const response = await ContactTypeList(attributes.organization, attributes.email);
            localStorage.setItem("ContactTypeList", JSON.stringify(response.data));
            setContactType(response.data);
        }
    }


    const fetchDataforDropdownsStateRegion = async () => {
        var StateRegionListLocal = await JSON.parse(localStorage.getItem("StateRegionList"));
        if (StateRegionListLocal) {
            setStateRegion(StateRegionListLocal);
        } else {
            const response = await StateRegionList(attributes.organization, attributes.email);
            localStorage.setItem("StateRegionList", JSON.stringify(response.data));
            setStateRegion(response.data);
        }

    }


    const addressObjectChangeHandler = (value) => {
        setAddressObject(value);
    }

    const addressValueChangeHandler = async (value) => {
        return Promise.resolve(setAddressValue(value));
    }


    const resetForm = async () => {
        return new Promise(resolve => {
            setTimeout(() => {
                resolve();
                setInputContactFirstName({error: ' ', value: ''})
                setInputLastName({error: ' ', value: ''})
                setInputNotificationEmail({error: ' ', value: ''})
                setInputNotificationPhone({error: ' ', value: ''})
                setInputNotificationAddress({error: ' ', value: ''})
                setInputNotificationContactType({error: ' ', value: ''})
                setInputNotificationCity({error: ' ', value: ''})
                setInputNotificationState({error: ' ', value: ''})
                setInputNotificationZip({error: ' ', value: ''});
                setAddressObject('');
                setAddressReset('reset')
                setAddressValue('');
            }, 600);
        });


    }


    const handleOnSubmit = async (event, userResponse = false) => {

        event.preventDefault();
        setLoading(true);
        setAddressReset('');
        // if (userResponse === 'false') {
        //     await addressCheck();
        // }


        var formsubmit = true;
        if (inputContactZipcode.value) {
            var zip = parseInt(inputContactZipcode.value);
            if (typeof zip === 'number') {
                if (zip.toString().length === 5) {
                    formsubmit = true;
                } else {
                    setLoading(false);
                    formsubmit = false;
                    setInputNotificationZip({...inputContactZipcode, error: "Please enter 5 digits zip code"})
                }
            } else {
                setLoading(false);
                formsubmit = false;
                setInputNotificationZip({...inputContactZipcode, error: "Please enter 5 digits zip code"})
            }
        }


        if (formsubmit === true) {
            if (userAgree === false && userResponse === false) {
                if ((addressObject || addressObject.streetLine) && inputContactCity.value && inputContactState.value && inputContactZipcode.value !== '') {
                    formsubmit = false;
                    await addressCheck(event);

                }
            }
        }
        if (formsubmit === true) {
            contactsCreate(event, userResponse);
        }


    }


    const contactsCreate = async (event, valid = null) => {


        let formData = new FormData();
        setInputContactFirstName({...inputContactFirstName, error: ' '})
        setInputLastName({...inputContactLastName, error: ' '})
        setInputNotificationEmail({...inputContactEmail, error: ' '})
        setInputNotificationPhone({...inputContactPhone, error: ' '})
        setInputNotificationAddress({...inputContactStreet, error: ' '})
        setInputNotificationContactType({...inputContactType, error: ' '})
        setInputNotificationCity({...inputContactCity, error: ' '})
        setInputNotificationState({...inputContactState, error: ' '})
        setInputNotificationZip({...inputContactZipcode, error: ' '})


        formData.append('entityId', props.match.params.id)
        formData.append('inputContactFirstName', inputContactFirstName.value)
        formData.append('inputContactLastName', inputContactLastName.value)
        formData.append('inputContactEmail', inputContactEmail.value)
        formData.append('inputContactPhone', inputContactPhone.value)
        formData.append('inputContactStreet', addressObject.streetLine)
        if (addressObject.streetLine) {
            formData.append('inputContactStreet', addressObject.streetLine)
        } else {
            formData.append('inputContactStreet', addressObject)
        }
        formData.append('inputContactType', inputContactType.value)

        if (inputContactCity.value) {
            formData.append('inputContactCity', inputContactCity.value);
        } else {
            formData.append('inputContactCity', '');
        }
        if (inputContactState.value) {
            formData.append('inputContactState', inputContactState.value);
        } else {
            formData.append('inputContactState', '');
        }

        formData.append('inputContactZipcode', inputContactZipcode.value)


        formData.append('inputAddressIsValid', valid ? valid : isValidAddress)
        formData.append('acceptInvalidAddress', userAgree)

        const response = await createContact(formData);
        if (response.field_error) {

            setLoading(false);
            setIsValidAddress(false);
            setUserAgree(false);
            Object.keys(response.field_error).forEach((key, index) => {

                if (key === 'inputContactFirstName') {
                    setInputContactFirstName({...inputContactFirstName, error: response.field_error[key]})
                }

                if (key === 'inputContactLastName') {
                    setInputLastName({...inputContactLastName, error: response.field_error[key]})
                }

                if (key === 'inputContactEmail') {
                    setInputNotificationEmail({...inputContactEmail, error: response.field_error[key]})
                }

                if (key === 'inputContactPhone') {
                    setInputNotificationPhone({...inputContactPhone, error: response.field_error[key]})
                }

                if (key === 'inputContactStreet') {
                    setInputNotificationAddress({...inputContactStreet, error: response.field_error[key]})
                }
                if (key === 'inputContactType') {
                    setInputNotificationContactType({...inputContactType, error: response.field_error[key]})
                }
                if (key === 'inputContactCity') {
                    setInputNotificationCity({...inputContactCity, error: response.field_error[key]})
                }
                if (key === 'inputContactState') {
                    setInputNotificationState({...inputContactState, error: response.field_error[key]})
                }
                if (key === 'inputContactZipcode') {
                    setInputNotificationZip({...inputContactZipcode, error: response.field_error[key]})
                }


            })
        }
        if (response.data) {
            if (response.data.type === 'ok') {

                new Promise(resolve => {
                    setTimeout(() => {
                        resolve();
                        setUserAgree(false);
                        setLoading(false);
                        setSuccessMessage(response.data.results);
                        resetForm();
                        window.scrollTo(0, 0)
                    }, 600);
                });

            }

            if (response.data) {
                if (response.data.type === 'error') {
                    await updateStates(response.data.results);

                    // console.log(addressObject);
                }
            }
        }

        //
        // for (var pair of formData.entries()) {
        //     console.log(pair[0] + ', ' + pair[1]);
        // }


        //
        // setTimeout(() => {
        //     setLoading(false);
        //     history.goBack();
        // }, 4000)
        // setLoading(true);


    }


    const updateStates = async (data) => {
        return new Promise(resolve => {
            setTimeout(() => {
                resolve();
                setUserAgree(false);
                setLoading(false);
                setErrorMessage(data);
                window.scrollTo(0, 0)
            }, 600);
        });


    }


    const addressCheck = async (event) => {
        var valid;
        let lookup1 = new Lookup();
        lookup1.street = addressObject.streetLine ? addressObject.streetLine : addressObject;
        lookup1.city = inputContactCity.value;
        lookup1.state = inputContactState.value;
        lookup1.zipCode = inputContactZipcode.value;
        const responseFromSmarty = client.send(lookup1).then(response => {
            valid = utils.isValid(response.lookups[0]);
            setIsValidAddress(valid);
            // response.lookups.map(lookup => console.log(lookup.result));

            // // Is lookup1 valid?
            // console.log('Is lookup1 valid?', utils.isValid(response.lookups[0]));
            //
            // // Is lookup1 invalid?
            // console.log('Is lookup1 invalid?', utils.isInvalid(response.lookups[0]));
            //
            // // Is lookup1 ambiguous?
            // console.log('// Is lookup1 ambiguous?', utils.isAmbiguous(response.lookups[0]));
            //
            // // Is lookup1 missing a secondary address?
            // console.log('// Is lookup1 missing a secondary address?', utils.isMissingSecondary(response.lookups[0]));
            if (valid === false) {
                setOpen(true);
            }

            if (valid === true) {
                contactsCreate(event, false);
            }
        });

    }

    const disableSuccessMessage = () => {
        setSuccessMessage(' ');
    }


    const disableErrorMessage = () => {
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
                        <Typography color="textPrimary">Add Contact</Typography>
                    </Breadcrumbs>
                </Paper>
            </div>



            <Dialog
                open={open}
                onClose={handleClose}
                aria-labelledby="alert-dialog-title"
                aria-describedby="alert-dialog-description"
            >
                <DialogTitle id="alert-dialog-title">{"We are unable to validate your address"}</DialogTitle>
                <DialogContent>
                    <DialogContentText id="alert-dialog-description">
                        Please make sure that you have entered it correctly.
                        If you proceed and we are unable to validate your address it may cause delays
                    </DialogContentText>
                </DialogContent>
                <DialogActions>
                    <Button onClick={handleClose} color="primary">
                        Cancel
                    </Button>
                    <Button onClick={(event) => iAgree(event)} color="primary" autoFocus>
                        Accept
                    </Button>
                </DialogActions>
            </Dialog>


            <Paper className={classes.paper} elevation={3}>

                            {successMessage !== ' ' ? (
                                <MySnackbarContentWrapper
                                    onClose={()=>{disableSuccessMessage()}}
                                    variant="success"
                                    message={successMessage}
                                />
                            ) : ''}


                            {errorMessage !== ' ' ? (
                                <MySnackbarContentWrapper
                                    onClose={()=>{disableErrorMessage()}}
                                    variant="error"
                                    message={errorMessage}
                                />
                            ) : ''}

                            <Grid container spacing={0}>
                                <form id={"create-course-form"} className={classes.container} onSubmit={handleOnSubmit}
                                      noValidate
                                      autoComplete="off">
                                    <FormGroup row>


                                        <div className={'col-md-6'}>
                                            <TextField
                                                disabled={loading}
                                                required
                                                error={inputContactFirstName.error !== ' '}
                                                label="First Name"
                                                value={inputContactFirstName.value}
                                                onChange={e => setInputContactFirstName({
                                                    ...inputContactFirstName,
                                                    value: e.target.value
                                                })}
                                                inputProps={{
                                                    name: 'inputContactFirstName',
                                                    id: 'inputContactFirstName',
                                                }}
                                                className={clsx(classes.textFieldtwofield, classes.dense)}
                                                margin="dense"
                                                helperText={inputContactFirstName.error}
                                            />
                                        </div>
                                        <div className={'col-md-6'}>
                                            <TextField
                                                disabled={loading}
                                                required
                                                value={inputContactLastName.value}
                                                onChange={e => setInputLastName({
                                                    ...inputContactLastName,
                                                    value: e.target.value
                                                })}
                                                inputProps={{
                                                    name: 'inputContactLastName',
                                                    id: 'inputContactLastName',
                                                }}
                                                error={inputContactLastName.error !== ' '}
                                                label="Last Name"
                                                className={clsx(classes.textFieldtwofield, classes.dense)}
                                                margin="dense"
                                                helperText={inputContactLastName.error}
                                            />
                                        </div>


                                        <div className={'col-md-6'}>
                                            <TextField
                                                disabled={loading}
                                                required

                                                type="email"
                                                value={inputContactEmail.value}
                                                onChange={e => setInputNotificationEmail({
                                                    ...inputContactEmail,
                                                    value: e.target.value
                                                })}
                                                inputProps={{
                                                    name: 'inputContactEmail',
                                                    id: 'inputContactEmail',
                                                }}
                                                error={inputContactEmail.error !== ' '}
                                                helperText={inputContactEmail.error}
                                                label="Email"
                                                className={clsx(classes.textFieldtwofield, classes.dense)}
                                                margin="dense"

                                            />
                                        </div>
                                        <div className={'col-md-6'}>
                                            <TextField
                                                disabled={loading}
                                                required
                                                value={inputContactPhone.value}
                                                onChange={e => setInputNotificationPhone({
                                                    ...inputContactPhone,
                                                    value: e.target.value
                                                })}
                                                inputProps={{
                                                    name: 'inputContactPhone',
                                                    id: 'inputContactPhone',
                                                }}
                                                error={inputContactPhone.error !== ' '}
                                                helperText={inputContactPhone.error}
                                                label="Phone"
                                                className={clsx(classes.textFieldtwofield, classes.dense)}
                                                margin="dense"
                                            />
                                        </div>
                                        <div className={'col-md-12'}>
                                            <Autocomplete
                                                disabled={loading}
                                                required
                                                width={''}
                                                addressObject={addressObjectChangeHandler}
                                                addressValue={addressValueChangeHandler}
                                                reset={addressReset}
                                                inputProps={{
                                                    name: 'inputContactStreet',
                                                    id: 'inputContactStreet',
                                                }}
                                                error={inputContactStreet.error !== ' '}
                                                helperText={inputContactStreet.error}
                                                className={clsx(classes.dense)}
                                            />
                                        </div>

                                        <div className={'col-md-4'}>
                                            <FormControl className={clsx(classes.selectField)}
                                                         error={inputContactType.error !== ' '}>
                                                <InputLabel htmlFor="age-native-simple">Contact Type</InputLabel>
                                                <Select
                                                    disabled={loading}
                                                    native
                                                    value={inputContactType.value}
                                                    onChange={e => setInputNotificationContactType({
                                                        ...inputContactType,
                                                        value: e.target.value
                                                    })}
                                                    inputProps={{
                                                        name: 'inputContactType',
                                                        id: 'inputContactType',
                                                    }}
                                                    error={inputContactType.error !== ' '}
                                                    helperText={inputContactType.error}
                                                >
                                                    <option value=""/>
                                                    {contactType?.map((anObjectMapped, index) => <option key={index}
                                                                                                         value={anObjectMapped.code}>{anObjectMapped.name}</option>)}

                                                </Select>
                                                <FormHelperText>{inputContactType.error}</FormHelperText>

                                            </FormControl>
                                        </div>


                                        <div className={clsx(classes.textFieldCity, 'col-md-4')}>

                                            <TextField
                                                id="standard-basic"
                                                disabled={loading}
                                                required
                                                error={inputContactCity.error !== ' '}
                                                helperText={inputContactCity.error}
                                                value={inputContactCity.value || ''}
                                                onChange={e => setInputNotificationCity({
                                                    ...inputContactCity,
                                                    value: e.target.value
                                                })}
                                                inputProps={{
                                                    name: 'inputContactCity',
                                                    id: 'inputContactCity',
                                                }}
                                                label="City"
                                                className={clsx(classes.textFieldtwofield, classes.dense)}
                                                margin="dense"

                                            />
                                        </div>






                                        <div className={'col-md-4'}>
                                            <FormControl className={clsx(classes.selectField)}
                                                         error={inputContactState.error !== ' '}>
                                                <InputLabel>
                                                    State/Region/Province
                                                </InputLabel>
                                                <Select
                                                    disabled={loading}
                                                    required
                                                    native
                                                    value={inputContactState.value || ''}
                                                    onChange={e => setInputNotificationState({
                                                        ...inputContactState,
                                                        value: e.target.value
                                                    })}
                                                    inputProps={{
                                                        name: 'inputContactState',
                                                        id: 'inputContactState',
                                                    }}
                                                    error={inputContactState.error !== ' '}
                                                    helperText={inputContactState.error}
                                                >
                                                    <option value=""/>
                                                    {StateRegion?.map((anObjectMapped, index) => <option key={index}
                                                                                                         value={anObjectMapped.code}>{anObjectMapped.name}</option>)}

                                                </Select>
                                                <FormHelperText>{inputContactState.error}</FormHelperText>
                                            </FormControl>
                                        </div>


                                        <div className={'col-md-6'}>
                                            <TextField
                                                disabled={loading}
                                                required
                                                type="text"
                                                value={inputContactZipcode.value}
                                                onChange={e => setInputNotificationZip({
                                                    ...inputContactZipcode,
                                                    value: e.target.value
                                                })}
                                                inputProps={{
                                                    name: 'inputContactZipcode',
                                                    id: 'inputContactZipcode',
                                                }}
                                                error={inputContactZipcode.error !== ' '}
                                                helperText={inputContactZipcode.error}
                                                label="Postal / Zip Code"
                                                className={clsx(classes.textFieldtwofield, classes.dense)}
                                                margin="dense"
                                            />
                                        </div>

                                        <div className={'col-md-12'}>
                                            <div className={clsx(classes.submitButton, 'custom-button-wrapper')}>
                                                {loading ? (
                                                        <div className={clsx(classes.loader)}>
                                                            <FacebookProgress/>
                                                        </div>)
                                                    : null}
                                                {/*<input className={clsx('btn btn-primary', classes.restButton)}*/}
                                                {/*       type="reset" onClick={(e) => {resetForm()}} value="Reset"/>*/}

                                                <input disabled={loading}
                                                       className={clsx('btn btn-primary', classes.restButton)}
                                                       type="submit" value="Add new Contact"/>
                                            </div>
                                        </div>
                                    </FormGroup>
                                </form>
                            </Grid>

            </Paper>
        </Layout>
    )
}

export default withOktaAuth(AddContactForm);
