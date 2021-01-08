import React, {useEffect, useContext} from "react";
import {makeStyles} from '@material-ui/core/styles';
import Grid from '@material-ui/core/Grid';
import {withOktaAuth} from '@okta/okta-react';
import {useOktaAuth} from "@okta/okta-react";
import ComplianceTaskList from '../entity/ComplianceTaskList';
import ContactList from '../entity/ContactList';
import PictureAsPdfIcon from '@material-ui/icons/PictureAsPdf';
import Link from "@material-ui/core/Link";
import {useHistory} from "react-router-dom";
import {entityDetail, selfEntityDetail} from '../api/enitity.crud';
import {UserContext} from '../context/UserContext';
import Breadcrumbs from "@material-ui/core/Breadcrumbs";
import Typography from "@material-ui/core/Typography";
import PropTypes from 'prop-types';
import ErrorIcon from '@material-ui/icons/Error';
import InfoIcon from '@material-ui/icons/Info';
import CloseIcon from '@material-ui/icons/Close';
import {amber, green} from '@material-ui/core/colors';
import IconButton from '@material-ui/core/IconButton';
import SnackbarContent from '@material-ui/core/SnackbarContent';
import WarningIcon from '@material-ui/icons/Warning';
import CheckCircleIcon from '@material-ui/icons/CheckCircle';
import clsx from 'clsx';
import Layout from "../layout/Layout";
import Paper from '@material-ui/core/Paper';
import PermIdentityIcon from '@material-ui/icons/PermIdentity';
import RoomIcon from '@material-ui/icons/Room';
import PersonIcon from '@material-ui/icons/Person';
import StreetviewIcon from '@material-ui/icons/Streetview';
import LocationCityIcon from '@material-ui/icons/LocationCity';
import MailIcon from '@material-ui/icons/Mail';
import Card from '@material-ui/core/Card';
import CardHeader from '@material-ui/core/CardHeader';
import CardContent from '@material-ui/core/CardContent';
import Avatar from '@material-ui/core/Avatar';
import FastForwardIcon from '@material-ui/icons/FastForward';
import Skeleton from '@material-ui/lab/Skeleton';
import AttachmentTable from "../attachment/AttachmentTable";
import Drawer from "@material-ui/core/Drawer";
import AdminSendMessageForm from "../message/AdminSendMessageForm";
import SendMessageForm from "../message/SendMessageForm";
import Button from "@material-ui/core/Button";
import AllMessages from "../message/AllMessages";
import Modal from "react-modal";
import NewChatPanel from "../message/NewChatPanel";
import List from "@material-ui/core/List";
import ListItem from "@material-ui/core/ListItem";
import ListItemAvatar from "@material-ui/core/ListItemAvatar";
import ListItemText from "@material-ui/core/ListItemText";
import Divider from "@material-ui/core/Divider";


const drawerWidth = 700;
const useStyles = makeStyles(theme => ({
    root: {
        flexGrow: 1,
        height: 250
    },
    paper: {
        padding: 20,
        height: 200,
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
    breadcrumbsDiv: {
        marginBottom: 30
    },

    breadcrumbsInner: {
        padding: 10
    },

    baseColor: {
        color: '#48465b'
    },
    // list: {
    //     width: 700,
    // },

    listInner:{
        // width: '100%'
    },

    messageSection: {
        marginBottom: 20,
        marginTop: 30
    },
    messageTitle: {
        marginLeft: 20,
        float: 'left'
    },

    rootDrawer: {
        display: 'flex',
    },
    drawer: {
        [theme.breakpoints.up('sm')]: {
            width: drawerWidth,
            flexShrink: 0,
        },
    },

    drawerPaper: {
        width: drawerWidth,
    },

    content: {
        flexGrow: 1,
        paddingLeft: theme.spacing(7),
        paddingRight: theme.spacing(7),
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

            elevation={6}
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

const ChildDetailedPage = (props) => {




    const {attributes, loading, addError, errorList, role, addTitle, removeError} = useContext(UserContext);
    const checkRole = role ? role : localStorage.getItem('role');
    const classes = useStyles();
    const history = useHistory();
    const [entitydetail, setEntitydetail] = React.useState()
    const [contactList, setContactList] = React.useState([])
    const [attachmentList, setAttachmentList] = React.useState([])
    const [taskList, setTaskList] = React.useState([])
    const [compliance, setComplainace] = React.useState(0);
    // const entity_id = attributes.organization;
    const [componentLoading, setComponentLoading] = React.useState(true);
    const [state, setState] = React.useState(false);
    const [modalIsOpen,setIsOpen] = React.useState(false);

    useEffect(() => {
        if (loading === true) {
            localStorage.setItem('activeEntityID', attributes.organization);
            fetchDetailedProfile();
        }
    }, [loading])


    const fetchDetailedProfile = async () => {

        var detailedView = '';

        if(checkRole === 'Child Entity'){
             detailedView = await entityDetail(attributes.organization);
        }
        if (detailedView.result) {
            addTitle('Dashboard ' +' - '+ detailedView.result.entity.name);
            localStorage.setItem('entityName', detailedView.result.entity.name);
            new Promise((resolve, reject) => {
                setEntitydetail(detailedView.result)
                setContactList(detailedView.result.contacts);
                setAttachmentList(detailedView.result.attachments)
                setTaskList(detailedView.result.tasks);
                setComponentLoading(false);
                resolve();
            });
        }
        if (detailedView.errors) {
            addError('Status '+ detailedView.errors.status +' '+ detailedView.errors.detail);
        }

        if (detailedView.type === 'error') {
            window.location.reload();
        }

        if (detailedView.status === 401) {
            window.location.reload();
        }


    }

    const updateComplianceTable = async() => {
        var detailedView = '';
        if (role === 'Parent Organization' || role === 'Administrator') {
            try {
                detailedView = await entityDetail(attributes.organization);
            } catch (e) {
                addError('Something went wrong with the API.');
            }
        }

        if (detailedView.result) {
            new Promise((resolve, reject) => {
                setTaskList(detailedView.result.tasks)
                resolve();
            });
        }
        if (detailedView.errors) {
            addError(detailedView.errors.detail);
        }
    }


    useEffect(() => {
        if(compliance !== 0) {
            updateComplianceTable()
        }
    },[compliance]);


    const UpdateComplainceState = () => {
        setComplainace(compliance + 1);
    }


    const contactData = {
        columns: [
            {title: 'Name', field: 'name'},
            {title: 'Phone', field: 'phone'},
            {title: 'Contact Type', field: 'contactType'},
            {title: 'email', field: 'email'},
            {title: 'Street', field: 'mailingStreet'},
            {title: 'City', field: 'mailingCity'},
            {title: 'State', field: 'mailingState'},
            {title: 'Zip', field: 'mailingZip'},

        ],
        data: contactList,
    };


    const taskData = {
        columns: [
            {title: 'Name', field: 'subject'},
            {title: 'Due Date', field: 'dueDate'},
            {title: 'Status', field: 'status'},
        ],
        data: taskList,
    };

    const attachmentData = {
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

    function openModal() {
        setIsOpen(true);
    }

    function afterOpenModal() {
        // references are now sync'd and can be accessed.
        // subtitle.style.color = '#f00';
    }

    function closeModal(){
        setIsOpen(false);
    }
    const toggleDrawer = (event, open) => {
        if (event.type === 'keydown' && (event.key === 'Tab' || event.key === 'Shift')) {
            return;
        }
        if (open) {
            setState(true);
        }

        if (!open) {
            setState(false);
        }
    };

    return (

        <>
                <Grid container spacing={2}>

                    {errorList?.map((value, index) => (
                        <MySnackbarContentWrapper key={index} className={classes.errorMessage} spacing={1} index={index} variant="error"
                                                  message={value} onClose={()=> removeError(index)}/>
                    ))}


                    <Drawer
                        ModalProps={{
                            keepMounted: true, // Better open performance on mobile.
                        }}
                        classes={{
                            paper: classes.drawerPaper,
                        }} anchor={'right'} open={state} onClose={(event) => toggleDrawer(event, false)}>
                        <div>
                            <main className={classes.content}>
                                {
                                    role === 'Administrator' ?
                                        <AdminSendMessageForm {...props} /> : ''
                                    // <SendMessageForm/> : ''
                                }

                                {
                                    role === 'Parent Organization' || role === 'Child Entity' ?
                                        <SendMessageForm {...props} /> : ''
                                }
                            </main>
                        </div>
                    </Drawer>

                    <Grid item xs={12} sm={4}>
                        <Card className={classes.root}>
                            <CardHeader
                                avatar={
                                    <Avatar aria-label="recipe" className={classes.avatar}>
                                        <PermIdentityIcon/>
                                    </Avatar>
                                }
                                title={
                                    <Typography className={classes.baseColor} gutterBottom variant="h5" component="h2">
                                        Company Info
                                    </Typography>
                                }
                                // subheader="September 14, 2016"
                            />

                            <CardContent>

                                    {entitydetail ?
                                        <>
                                        <ul className={classes.companyinfo}>
                                            <li className={classes.listItem}><strong>State ID:</strong> {entitydetail.entity.stateId ? entitydetail.entity.stateId : ''}</li>
                                            <li className={classes.listItem}><strong>Formation
                                                Date:</strong> {entitydetail.entity.formationDate ? entitydetail.entity.formationDate : ''}
                                            </li>
                                            <li className={classes.listItem}><strong>Registered Agent Expiration
                                                Date: </strong> {entitydetail.entity.expirationDate}</li>
                                            {/*<li className={classes.listItem}><strong>Tax ID:</strong> 09890890</li>*/}
                                        </ul> </>:

                                       <>
                                            <Skeleton />
                                            <Skeleton />
                                            <Skeleton />
                                            <Skeleton />
                                            <Skeleton />
                                        </>
                                    }

                            </CardContent>

                        </Card>
                    </Grid>
                    <Grid item xs={12} sm={4}>
                        <Card className={classes.root}>
                            <CardHeader
                                avatar={
                                    <Avatar aria-label="recipe" className={classes.avatar}>
                                        <RoomIcon/>
                                    </Avatar>
                                }
                                title={
                                    <Typography className={classes.baseColor} gutterBottom variant="h5" component="h2">
                                        RA Address
                                    </Typography>
                                }
                                subheader={
                                    entitydetail ? entitydetail.registerAgent.name : ''
                                }
                            />

                            <CardContent>

                                    {entitydetail ?
                                        <>
                                        <ul className={classes.companyinfo}>
                                            <li className={classes.listItem}><PersonIcon
                                                className={classes.adjustment}/>
                                                <strong>{entitydetail.registerAgent.fileAs}</strong></li>
                                            <li className={classes.listItem}><StreetviewIcon
                                                className={classes.adjustment}/> {entitydetail.registerAgent.address}
                                            </li>
                                            <li className={classes.listItem}><RoomIcon
                                                className={classes.adjustment}/>{entitydetail.registerAgent.address2}
                                            </li>
                                            <li className={classes.listItem}><LocationCityIcon
                                                className={classes.adjustment}/> {entitydetail.registerAgent.city}, {entitydetail.registerAgent.state} {entitydetail.registerAgent.zipcode}
                                            </li>
                                        </ul>
                                        </> :
                                        <>
                                           <Skeleton />
                                           <Skeleton />
                                           <Skeleton />
                                           <Skeleton />
                                           <Skeleton />
                                       </>
                                    }

                            </CardContent>

                        </Card>
                    </Grid>
                    <Grid item xs={12} sm={4}>
                        <Card className={classes.root}>
                            <CardHeader
                                avatar={
                                    <Avatar aria-label="recipe" className={classes.avatar}>
                                        <FastForwardIcon/>
                                    </Avatar>
                                }
                                title={
                                    <Typography className={classes.baseColor} gutterBottom variant="h5" component="h2">
                                        Forwarding Address
                                    </Typography>
                                }
                                // subheader="September 14, 2016"
                            />

                            <CardContent>

                                    {entitydetail ?
                                        <>
                                        <ul className={classes.companyinfo}>
                                            {entitydetail.entity.shippingStreet ?
                                                <li className={classes.listItem}><RoomIcon
                                                    className={classes.adjustment}/>
                                                    <strong>{entitydetail.entity.shippingStreet}, {entitydetail.entity.shippingCity}, {entitydetail.entity.shippingState} {entitydetail.entity.shippingCode} </strong>
                                                </li> : ''}
                                            <li className={classes.listItem}><MailIcon
                                                className={classes.adjustment}/> {entitydetail.entity.email}
                                            </li>
                                        </ul>
                                        </>
                                        :
                                        <>
                                            <Skeleton />
                                            <Skeleton />
                                            <Skeleton />
                                            <Skeleton />
                                            <Skeleton />
                                        </>

                                    }

                            </CardContent>

                        </Card>
                    </Grid>
                </Grid>


            <Paper className={classes.messageSection} elevation={2}>
                <Grid container spacing={5}>

                    <Grid item xs={12}>
                        <div className="messageSection">
                            <Typography className={classes.messageTitle} variant="h5" component="h2"
                                        color="textPrimary">Messages</Typography>
                            <Button variant="outlined" color="primary" className={'sendMessageButton'}
                                    onClick={(event) => toggleDrawer(event, true)}>Send Message</Button>
                        </div>
                        { entitydetail ?
                            <AllMessages entityName={entitydetail.entity.name} openmodal={openModal}/> :
                            <List>
                                <ListItem alignItems="flex-start">
                                    <ListItemAvatar>
                                        <Skeleton variant="circle" height={50} width={50} animation="wave"/>
                                    </ListItemAvatar>
                                    <ListItemText
                                        primary={
                                            <React.Fragment>
                                                <Skeleton height={30} width={'100%'} animation="wave"/>
                                            </React.Fragment>
                                        }
                                        secondary={
                                            <React.Fragment>
                                                <Skeleton height={50} width={'100%'} animation="wave"/>
                                            </React.Fragment>
                                        }
                                    />
                                </ListItem>
                                <Divider variant="inset" component="li"/>
                                <ListItem alignItems="flex-start">
                                    <ListItemAvatar>
                                        <Skeleton variant="circle" height={50} width={50} animation="wave"/>
                                    </ListItemAvatar>
                                    <ListItemText
                                        primary={
                                            <React.Fragment>
                                                <Skeleton height={30} width={'100%'} animation="wave"/>
                                            </React.Fragment>
                                        }
                                        secondary={
                                            <React.Fragment>
                                                <Skeleton height={50} width={'100%'} animation="wave"/>
                                            </React.Fragment>
                                        }
                                    />
                                </ListItem>
                                <Divider variant="inset" component="li"/>
                                <ListItem alignItems="flex-start">
                                    <ListItemAvatar>
                                        <Skeleton variant="circle" height={50} width={50} animation="wave"/>
                                    </ListItemAvatar>
                                    <ListItemText
                                        primary={
                                            <React.Fragment>
                                                <Skeleton height={30} width={'100%'} animation="wave"/>
                                            </React.Fragment>
                                        }
                                        secondary={
                                            <React.Fragment>
                                                <Skeleton height={50} width={'100%'} animation="wave"/>
                                            </React.Fragment>
                                        }
                                    />
                                </ListItem>
                                <Divider variant="inset" component="li"/>
                                <ListItem alignItems="flex-start">
                                    <ListItemAvatar>
                                        <Skeleton variant="circle" height={50} width={50} animation="wave"/>
                                    </ListItemAvatar>
                                    <ListItemText
                                        primary={
                                            <React.Fragment>
                                                <Skeleton height={30} width={'100%'} animation="wave"/>
                                            </React.Fragment>
                                        }
                                        secondary={
                                            <React.Fragment>
                                                <Skeleton height={50} width={'100%'} animation="wave"/>
                                            </React.Fragment>
                                        }
                                    />
                                </ListItem>
                                <Divider variant="inset" component="li"/>
                            </List>
                        }
                    </Grid>

                </Grid>
            </Paper>
                <Grid container spacing={5}>

                    <Grid item xs={12}>
                        <ComplianceTaskList update={UpdateComplainceState} loading={componentLoading} tooltip={'Add New Contact'} data={taskData}
                                            title={'Compliance Tasks'} eid={attributes ? attributes.organization : ''}/>
                    </Grid>
                </Grid>
                <Grid container spacing={5}>
                    <Grid item xs={12}>
                        <AttachmentTable action={true} loading={componentLoading} tooltip={'Add New Document'}
                                     redirect={true} url={`/attachment/form/add/${attributes ? attributes.organization : ''}`} data={attachmentData}
                                     title={'Documents'}/>
                    </Grid>

                </Grid>
                <Grid container spacing={5}>
                    <Grid item xs={12}>
                        <ContactList action={true} loading={componentLoading} tooltip={'Add New Contact'}
                                     redirect={true}
                                     url={`/contact/form/add/${attributes ? attributes.organization : ''}`} data={contactData}
                                     title={'Contacts'}/>
                    </Grid>
                </Grid>


            <div>
                <Modal
                    parentSelector={() => document.querySelector('#messageModal')}
                    isOpen={modalIsOpen}
                    onAfterOpen={afterOpenModal}
                    onRequestClose={closeModal}
                    contentLabel="Chat Application"
                    style={{
                        overlay: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)'
                        },

                    }}
                >
                    <div className="chat-wrapper">
                        <NewChatPanel/>
                    </div>
                </Modal>
            </div>

        </>

    )
}

export default withOktaAuth(ChildDetailedPage);

