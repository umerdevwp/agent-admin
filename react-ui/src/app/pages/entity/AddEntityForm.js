import React from 'react';
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

const AddEntityForm = (props) => {


    const classes = useStyles();
    const inputLabel = React.useRef(null);
    const [labelWidth, setLabelWidth] = React.useState(0);
    const [addressObject, setAddressObject] = React.useState([]);
    const [addressValue, setAddressValue] = React.useState('');
    const [loading, setLoading] = React.useState(false)
    const [error, setError] = React.useState(false)
    //form state

    const [inputName, setInputName] = React.useState({value: '', error: ' ',});
    const [fillingState, setFillingState] = React.useState({value: '', error: ' '});
    const [inputComplianceOnly, setInputComplianceOnly] = React.useState({value: '', error: ' '});
    const [inputFillingState, setInputFillingState] = React.useState({value: '', error: ' '});
    const [inputFillingStructure, setInputFillingStructure] = React.useState({value: '', error: ' '});
    const [inputFormationDate, setInputFormationDate] = React.useState({value: '', error: ' '});
    const [inputFirstName, setInputFirstName] = React.useState({value: '', error: ' '});
    const [inputLastName, setInputLastName] = React.useState({value: '', error: ' '});
    const [inputNotificationEmail, setInputNotificationEmail] = React.useState({value: '', error: ' '});
    const [inputNotificationPhone, setInputNotificationPhone] = React.useState({value: '', error: ' '});
    const [inputNotificationAddress, setInputNotificationAddress] = React.useState({value: '', error: ' '});
    const [inputNotificationContactType, setInputNotificationContactType] = React.useState({value: '', error: ' '});
    const [inputNotificationCity, setInputNotificationCity] = React.useState({value: '', error: ' '});
    const [inputNotificationState, setInputNotificationState] = React.useState({value: '', error: ' '});
    const [inputNotificationZip, setInputNotificationZip] = React.useState({value: '', error: ' '});
    const [inputFiling, setInputFiling] = React.useState({value: '', error: ' '});
    const [inputBusinessPurpose, setInputBusinessPurpose] = React.useState({value: '', error: ' '});


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
                setInputNotificationCity({...inputNotificationCity, value: addressObject.city})
                setInputNotificationState({...inputNotificationState, value: addressObject.state})
            }
        }

    }, [addressObject, addressValue])


    const addressObjectChangeHandler = (value) => {
        setAddressObject(value);
    }

    const addressValueChangeHandler = (value) => {
        setAddressValue(value);
    }

    const handleClose = (event, reason) => {
        setError(false);
    }

    const handleOnSubmit = (event) => {
        event.preventDefault();
        let formData = new FormData();

        // console.log(inputFormationDate.value);
        formData.append('asdasdas', 'asdasdasdasdas')
        // Display the key/value pairs


        formData.append('inputName', inputName.value)
        formData.append('inputComplianceOnly', inputComplianceOnly.value)
        formData.append('inputFillingState', inputFillingState.value)
        formData.append('inputFillingStructure', inputFillingStructure.value)
        formData.append('inputFormationDate', inputFormationDate.value)
        formData.append('inputFirstName', inputFirstName.value)
        formData.append('inputLastName', inputLastName.value)
        formData.append('inputNotificationEmail', inputNotificationEmail.value)
        formData.append('inputNotificationPhone', inputNotificationPhone.value)
        formData.append('inputNotificationAddress', addressObject.text)
        formData.append('inputNotificationContactType', inputNotificationContactType.value)
        formData.append('inputNotificationCity', inputNotificationCity.value)
        formData.append('inputNotificationState', inputNotificationState.value)
        formData.append('inputNotificationZip', inputNotificationZip.value)
        formData.append('inputFiling', inputFiling.value)
        for (var pair of formData.entries()) {
            console.log(pair[0] + ', ' + pair[1]);
        }

        setTimeout(() => {
            setInputName({...inputName, error: 'Field is required'})
            setLoading(false);
            setError(true)
        }, 4000)
        setLoading(true);
        console.log('Info', `Welcome ${inputName.value}`);
    }

    return (

        <div className={classes.root}>
            <Title title={'Add New Entity'}/>
            <Grid container spacing={1}>
                <Grid item xs={12}>
                    <Portlet>
                        <PortletHeader icon={<PermIdentityIcon className={classes.adjustment}/>}
                                       title="New RA Client"/>
                        <PortletBody>
                            {error ? (<MySnackbarContentWrapper
                                onClose={handleClose}
                                variant="error"
                                message="Something went wrong"
                            />) : ''}

                            <div className="row">
                                <form className={classes.container} onSubmit={handleOnSubmit} noValidate
                                      autoComplete="off">
                                    <FormGroup row>
                                        <div className={'col-md-6'}>
                                            <TextField
                                                disabled={loading}
                                                required
                                                error={inputName.error !== ' '}
                                                onChange={e => setInputName({...inputName, value: e.target.value})}
                                                id="inputName"
                                                label="Entity Name"
                                                className={clsx(classes.textField, classes.dense)}
                                                margin="dense"
                                                helperText={inputName.error}
                                            />
                                        </div>
                                        <div className={'col-md-6'}>
                                            <FormControlLabel
                                                disabled={loading}
                                                onChange={e => setInputComplianceOnly({
                                                    ...inputComplianceOnly,
                                                    value: e.target.value
                                                })}
                                                value={inputComplianceOnly}
                                                control={<Checkbox color="primary"/>}
                                                label="Compliance Only"
                                                className={clsx(classes.textField, classes.checkbox)}
                                                labelPlacement="start"
                                            />
                                        </div>
                                        <div className={'col-md-4'}>
                                            <FormControl className={clsx(classes.selectField)}>
                                                <InputLabel htmlFor="age-native-simple">Filling State</InputLabel>
                                                <Select
                                                    disabled={loading}
                                                    required
                                                    native
                                                    value={inputFillingState.value}
                                                    onChange={e => setInputFillingState({
                                                        ...inputFillingState,
                                                        value: e.target.value
                                                    })}
                                                    inputProps={{
                                                        name: 'inputFillingState',
                                                        id: 'inputFillingState',
                                                    }}>
                                                    <option value=""/>
                                                    {StateRegion.map((anObjectMapped, index) => <option key={index}
                                                                                                        value={anObjectMapped.value}>{anObjectMapped.label}</option>)}

                                                </Select>
                                            </FormControl>
                                        </div>
                                        <div className={'col-md-4'}>
                                            <FormControl className={clsx(classes.selectField)}>
                                                <InputLabel htmlFor="age-native-simple">Filling
                                                    Structure</InputLabel>
                                                <Select
                                                    disabled={loading}
                                                    required
                                                    native
                                                    value={inputFillingStructure.value}
                                                    onChange={e => setInputFillingStructure({
                                                        ...inputFillingStructure,
                                                        value: e.target.value
                                                    })}
                                                    inputProps={{
                                                        name: 'inputFillingStructure',
                                                        id: 'inputFillingStructure',
                                                    }}>
                                                    <option value=""/>
                                                    {FillingStructureData.map((anObjectMapped, index) => <option
                                                        key={index}
                                                        value={anObjectMapped.value}>{anObjectMapped.value}</option>)}

                                                </Select>
                                            </FormControl>
                                        </div>
                                        <div className={'col-md-4'}>
                                            <TextField
                                                disabled={loading}
                                                required
                                                label="Formation Date"
                                                format={'Y-M-d'}
                                                value={inputFormationDate.value}
                                                onChange={e => setInputFormationDate({
                                                    ...inputFormationDate,
                                                    value: e.target.value
                                                })}
                                                inputProps={{
                                                    name: 'inputFormationDate',
                                                    id: 'inputFormationDate',
                                                }}

                                                InputLabelProps={{
                                                    shrink: true,
                                                }}
                                                type="date"
                                                className={clsx(classes.textFieldOther, classes.dense)}

                                            />
                                        </div>
                                        <div className={'col-md-6'}>
                                            <TextField
                                                disabled={loading}
                                                required
                                                error={inputFirstName.error !== ' '}
                                                label="First Name"
                                                value={inputFirstName.value}
                                                onChange={e => setInputFirstName({
                                                    ...inputFirstName,
                                                    value: e.target.value
                                                })}
                                                inputProps={{
                                                    name: 'inputFirstName',
                                                    id: 'inputFirstName',
                                                }}
                                                className={clsx(classes.textFieldtwofield, classes.dense)}
                                                margin="dense"
                                                helperText={inputFirstName.error}
                                            />
                                        </div>
                                        <div className={'col-md-6'}>
                                            <TextField
                                                disabled={loading}
                                                required
                                                value={inputLastName.value}
                                                onChange={e => setInputLastName({
                                                    ...inputLastName,
                                                    value: e.target.value
                                                })}
                                                inputProps={{
                                                    name: 'inputLastName',
                                                    id: 'inputLastName',
                                                }}
                                                error={inputLastName.error !== ' '}
                                                label="Last Name"
                                                className={clsx(classes.textFieldtwofield, classes.dense)}
                                                margin="dense"
                                                helperText={inputLastName.error}
                                            />
                                        </div>
                                        <div className={'col-md-6'}>
                                            <TextField
                                                disabled={loading}
                                                required

                                                type="email"
                                                value={inputNotificationEmail.value}
                                                onChange={e => setInputNotificationEmail({
                                                    ...inputNotificationEmail,
                                                    value: e.target.value
                                                })}
                                                inputProps={{
                                                    name: 'inputNotificationEmail',
                                                    id: 'inputNotificationEmail',
                                                }}
                                                error={inputNotificationEmail.error !== ' '}
                                                helperText={inputNotificationEmail.error}
                                                label="Notification Email"
                                                className={clsx(classes.textFieldtwofield, classes.dense)}
                                                margin="dense"

                                            />
                                        </div>
                                        <div className={'col-md-6'}>
                                            <TextField
                                                disabled={loading}
                                                required
                                                value={inputNotificationPhone.value}
                                                onChange={e => setInputNotificationPhone({
                                                    ...inputNotificationPhone,
                                                    value: e.target.value
                                                })}
                                                inputProps={{
                                                    name: 'inputNotificationPhone',
                                                    id: 'inputNotificationPhone',
                                                }}
                                                error={inputNotificationPhone.error !== ' '}
                                                helperText={inputNotificationPhone.error}
                                                label="Notification Phone"
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
                                                    name: 'inputNotificationAddress',
                                                    id: 'inputNotificationAddress',
                                                }}
                                                error={inputNotificationAddress.error !== ' '}
                                                helperText={inputNotificationAddress.error}
                                                className={clsx(classes.dense)}
                                            />
                                        </div>

                                        <div className={'col-md-4'}>
                                            <FormControl className={clsx(classes.selectField)}>
                                                <InputLabel htmlFor="age-native-simple">Contact Type</InputLabel>
                                                <Select
                                                    disabled={loading}
                                                    native
                                                    value={inputNotificationContactType.value}
                                                    onChange={e => setInputNotificationContactType({
                                                        ...inputNotificationContactType,
                                                        value: e.target.value
                                                    })}
                                                    inputProps={{
                                                        name: 'inputNotificationContactType',
                                                        id: 'inputNotificationContactType',
                                                    }}
                                                    error={inputNotificationContactType.error !== ' '}
                                                    helperText={inputNotificationContactType.error}
                                                >
                                                    <option value=""/>
                                                    {contactType.map((anObjectMapped, index) => <option key={index}
                                                                                                        value={anObjectMapped.value}>{anObjectMapped.value}</option>)}

                                                </Select>
                                            </FormControl>
                                        </div>
                                        <div className={clsx(classes.textFieldCity, 'col-md-4')}>
                                            <TextField
                                                disabled={loading}
                                                required
                                                value={inputNotificationCity.value}
                                                onChange={e => setInputNotificationCity({
                                                    ...inputNotificationCity,
                                                    value: e.target.value
                                                })}
                                                inputProps={{
                                                    name: 'inputNotificationCity',
                                                    id: 'inputNotificationCity',
                                                }}
                                                error={inputNotificationCity.error !== ' '}
                                                helperText={inputNotificationCity.error}
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
                                                    value={inputNotificationState.value}
                                                    onChange={e => setInputNotificationState({
                                                        ...inputNotificationState,
                                                        value: e.target.value
                                                    })}
                                                    inputProps={{
                                                        name: 'inputNotificationState',
                                                        id: 'inputNotificationState',
                                                    }}
                                                    error={inputNotificationState.error !== ' '}
                                                    helperText={inputNotificationState.error}
                                                >
                                                    <option value=""/>
                                                    {StateRegion.map((anObjectMapped, index) => <option key={index}
                                                                                                        value={anObjectMapped.value}>{anObjectMapped.label}</option>)}

                                                </Select>
                                            </FormControl>
                                        </div>
                                        <div className={'col-md-6'}>
                                            <TextField
                                                disabled={loading}
                                                required
                                                type="text"
                                                value={inputNotificationZip.value}
                                                onChange={e => setInputNotificationZip({
                                                    ...inputNotificationZip,
                                                    value: e.target.value
                                                })}
                                                inputProps={{
                                                    name: 'inputNotificationZip',
                                                    id: 'inputNotificationZip',
                                                }}
                                                error={inputNotificationZip.error !== ' '}
                                                label="Postal / Zip Code"
                                                className={clsx(classes.textFieldtwofield, classes.dense)}
                                                margin="dense"
                                            />
                                        </div>
                                        <div className={'col-md-6'}>
                                            <CustomFileInput
                                                disabled={loading}
                                                required
                                                id="attachment"
                                                value={inputFiling.value.File}
                                                onChange={e => setInputFiling({
                                                    ...inputFiling,
                                                    value: e.target.files[0]
                                                })}
                                                label="Attachment"
                                                className={clsx(classes.fileUploading, classes.dense)}
                                                margin="dense"
                                                invalid={inputFiling.error !== ' '}
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
                                                       type="submit" value="Create New Entity"/>

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


export default withAuth(AddEntityForm);
