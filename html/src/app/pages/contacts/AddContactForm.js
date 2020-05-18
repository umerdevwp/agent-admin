import React, {useContext} from 'react';
import {makeStyles} from '@material-ui/core/styles';
import Button from '@material-ui/core/Button';
import Typography from '@material-ui/core/Typography';
import Container from "@material-ui/core/Container";
import Grid from "@material-ui/core/Grid";
import {
    Portlet,
    PortletBody,
    PortletHeader,
    PortletHeaderToolbar
} from "../../partials/content/Portlet";
import {Title} from '../home/helpers/titles'
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
import moment from 'moment';
import Downshift from "downshift";
import Autocomplete from "../entity/TestAutocomplete";
import CustomFileInput from "reactstrap/es/CustomFileInput";
import CircularProgress from '@material-ui/core/CircularProgress';
import {contactType, StateRegion, FillingStructureData} from '../../StaticData/Static';
import {withAuth} from '@okta/okta-react';

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
import {ContactTypeList, EntitytypesList, StateRegionList} from "../../crud/enitity.crud";
import {createContact} from "../../crud/contact.crud";
import {OktaUserContext} from "../../context/OktaUserContext";


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


    const {oktaprofile, isAdmin} = useContext(OktaUserContext);

    const inputLabel = React.useRef(null);
    const [labelWidth, setLabelWidth] = React.useState(0);
    const [addressObject, setAddressObject] = React.useState([]);
    const [addressValue, setAddressValue] = React.useState('');
    const [loading, setLoading] = React.useState(false)
    const [contactType, setContactType] = React.useState([]);
    const [successMessage, setSuccessMessage] = React.useState(false);
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
                setInputNotificationCity({...inputContactCity, value: addressObject.city})
                setInputNotificationState({...inputContactState, value: addressObject.state})
            }
        }

    }, [addressObject, addressValue])


    React.useEffect(() => {
        fetchDataforDropdownsContactTypeList();
        fetchDataforDropdownsStateRegion();
    }, [])


    const fetchDataforDropdownsContactTypeList = async () => {
        const response = await ContactTypeList(oktaprofile.organization, oktaprofile.email);
        setContactType(response.data);
    }


    const fetchDataforDropdownsStateRegion = async () => {
        const response = await StateRegionList(oktaprofile.organization, oktaprofile.email);
        setStateRegion(response.data);

    }


    const addressObjectChangeHandler = (value) => {
        setAddressObject(value);
    }

    const addressValueChangeHandler = (value) => {
        setAddressValue(value);
    }

    const handleClose = (event, reason) => {
        if (reason === 'clickaway') {
            return;
        }
    }

    const handleOnSubmit = async (event) => {
        event.preventDefault();
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
        formData.append('inputContactStreet', addressObject.text)
        formData.append('inputContactType', inputContactType.value)
        formData.append('inputContactCity', inputContactCity.value)
        formData.append('inputContactState', inputContactState.value)
        formData.append('inputContactZipcode', inputContactZipcode.value)


        const response = await createContact(formData);
        if (response.field_error) {


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


        if (response) {
            if (response.status) {
                setSuccessMessage(true);
            }
        }

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

    return (

        <div className={classes.root}>
            <Title title={'Contact'}/>
            <Grid container spacing={1}>
                <Grid item xs={12}>
                    <Portlet>
                        <PortletHeader icon={<PermIdentityIcon className={classes.adjustment}/>}
                                       title="Add new contact"/>
                        <PortletBody>
                            {successMessage ? (
                                <MySnackbarContentWrapper
                                    onClose={handleClose}
                                    variant="success"
                                    message="Contact has been added"
                                />
                            ) : ''}
                            <div className="row">
                                <form className={classes.container} onSubmit={handleOnSubmit} noValidate
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
                                            <FormControl className={clsx(classes.selectField)}>
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
                                            </FormControl>
                                        </div>


                                        <div className={clsx(classes.textFieldCity, 'col-md-4')}>
                                            <TextField
                                                disabled={loading}
                                                required
                                                value={inputContactCity.value}
                                                onChange={e => setInputNotificationCity({
                                                    ...inputContactCity,
                                                    value: e.target.value
                                                })}
                                                inputProps={{
                                                    name: 'inputContactCity',
                                                    id: 'inputContactCity',
                                                }}
                                                error={inputContactCity.error !== ' '}
                                                helperText={inputContactCity.error}
                                                label="City"
                                                className={clsx(classes.textFieldtwofield, classes.dense)}
                                                margin="dense"

                                            />
                                        </div>
                                        <div className={'col-md-4'}>
                                            <FormControl className={clsx(classes.selectField)}>
                                                <InputLabel
                                                    htmlFor="state-native-simple">State/Region/Province</InputLabel>
                                                <Select
                                                    disabled={loading}
                                                    required
                                                    native
                                                    value={inputContactState.value}
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
                                                <input className={clsx('btn btn-primary', classes.restButton)}
                                                       type="submit" value="Reset"/>

                                                <input className={clsx('btn btn-primary', classes.restButton)}
                                                       type="submit" value="Add new Contact"/>
                                            </div>
                                        </div>
                                    </FormGroup>
                                </form>
                            </div>
                        </PortletBody>
                    </Portlet>

                </Grid>
            </Grid>
        </div>
    )
}


export default withAuth(AddContactForm);
