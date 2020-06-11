import React, {useEffect, useMemo, useContext} from "react";
import {makeStyles} from '@material-ui/core/styles';
import Paper from '@material-ui/core/Paper';
import Grid from '@material-ui/core/Grid';
import {withAuth} from '@okta/okta-react';
import Box from '@material-ui/core/Box';
import Button from '@material-ui/core/Button';
import FastForwardIcon from '@material-ui/icons/FastForward';
import AttachmentIcon from '@material-ui/icons/Attachment';
import ViewListIcon from '@material-ui/icons/ViewList';
import ContactsIcon from '@material-ui/icons/Contacts';
import EntityListing from '../entity/EntityListing';
import ContactList from '../entity/ContactList';
import {metronic} from "../../../_metronic";
import QuickStatsChart from "../../widgets/QuickStatsChart";
import OrderStatisticsChart from "../../widgets/OrderStatisticsChart";
import OrdersWidget from "../../widgets/OrdersWidget";
import SalesBarChart from "../../widgets/SalesBarChart";
import {useSelector} from "react-redux";
import {
    Portlet,
    PortletBody,
    PortletHeader,
    PortletHeaderToolbar
} from "../../partials/content/Portlet";
import PortletHeaderDropdown from "../../partials/content/CustomDropdowns/PortletHeaderDropdown";
import {Title} from '../home/helpers/titles'
import PermIdentityIcon from '@material-ui/icons/PermIdentity';
import RoomIcon from '@material-ui/icons/Room';
import PersonIcon from '@material-ui/icons/Person';
import StreetviewIcon from '@material-ui/icons/Streetview';
import PictureAsPdfIcon from '@material-ui/icons/PictureAsPdf';
import LocationCityIcon from '@material-ui/icons/LocationCity';
import BusinessIcon from '@material-ui/icons/Business';
import MailIcon from '@material-ui/icons/Mail';
import Container from "@material-ui/core/Container";
import Link from "@material-ui/core/Link";
import {useHistory} from "react-router-dom";
import {entityDetail} from '../../crud/enitity.crud';
import {OktaUserContext} from '../../context/OktaUserContext';
import Breadcrumbs from "@material-ui/core/Breadcrumbs";
import Typography from "@material-ui/core/Typography";


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


const EntityDetailedPage = (props) => {


    const entity_id = props.entityid ? props.entityid : props.match.params.id;
    const breadcrumbs_show = !props.breadcrumbz ? props.breadcrumbz : true;
    const HOST = process.env.REACT_APP_SERVER_API_URL;

    const {oktaprofile, isAdmin} = useContext(OktaUserContext);
    const classes = useStyles();
    const history = useHistory();
    const [entitydetail, setEntitydetail] = React.useState()
    const [contactList, setContactList] = React.useState([])
    const [attachmentList, setAttachmentList] = React.useState([])
    const [taskList, setTaskList] = React.useState([])

    const [loading, setLoading] = React.useState(true)
    useEffect(() => {
        fetchDetailedProfile();
    }, [])


    const fetchDetailedProfile = async () => {
        const detailedView = await entityDetail(oktaprofile.organization, oktaprofile.email, entity_id);
        setEntitydetail(detailedView.result)
        setContactList(detailedView.result.contacts);
        setAttachmentList(detailedView.result.attachments)
        setTaskList(detailedView.result.tasks)
        setLoading(false);
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

    const formatBytes = (bytes, decimals = 2) => {
        if (bytes === 0) return '0 Bytes';

        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

        const i = Math.floor(Math.log(bytes) / Math.log(k));

        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }


    const attachmentData = {
        columns: [
            {
                title: 'File Name',
                editable: 'never',
                render: rowData => <a target="_blank"
                                      href={`${HOST}/download/file/${rowData.fid}?name=${rowData.name}`}>
                    <PictureAsPdfIcon/> {rowData.name}
                </a>
            },
            {title: 'Date', field: 'created'},
            {title: 'Size', field: 'fileSize'},
        ],
        data: attachmentList,
    };


    return (

        <>

            <div>
                <h3>Navigation</h3>
                <Breadcrumbs aria-label="breadcrumb">
                    <Link color="inherit" onClick={(e) => {
                        history.goBack()
                    }}>
                        Dashboard
                    </Link>
                    <Typography color="textPrimary">Entity</Typography>
                </Breadcrumbs>
            </div>


            {entitydetail ? <Title title={entitydetail.entity.name}/> : <Title title={''}/>}

            <Grid container spacing={1}>
                <Grid item xs={12} sm={4}>
                    <Portlet fluidHeight={true}>
                        <PortletHeader icon={<PermIdentityIcon className={classes.adjustment}/>} title="Company Info"/>
                        <PortletBody>
                            {entitydetail ?

                                <ul className={classes.companyinfo}>
                                    <li className={classes.listItem}><strong>State ID:</strong> 0</li>
                                    <li className={classes.listItem}><strong>Formation
                                        Date:</strong> {entitydetail.entity.formationDate}</li>
                                    <li className={classes.listItem}><strong>Registered Agent Expiration
                                        Date: </strong> {entitydetail.entity.expirationDate}</li>
                                    {/*<li className={classes.listItem}><strong>Tax ID:</strong> 09890890</li>*/}
                                </ul> :

                                <ul className={classes.companyinfo}>
                                    <li className={classes.listItem}><strong>State ID:</strong> -</li>
                                    <li className={classes.listItem}><strong>Formation Date:</strong> -</li>
                                    <li className={classes.listItem}><strong>Expiration Date: </strong> -</li>
                                    <li className={classes.listItem}><strong>Tax ID:</strong> -</li>
                                </ul>
                            }
                        </PortletBody>
                    </Portlet>
                </Grid>


                <Grid item xs={12} sm={4}>
                    <Portlet fluidHeight={true}>
                        <PortletHeader icon={<RoomIcon className={classes.adjustment}/>} title="RA Address"/>
                        <PortletBody>
                            {entitydetail ?

                                <ul className={classes.companyinfo}>
                                    <li className={classes.listItem}>
                                        <strong>{entitydetail.registerAgent.name}</strong></li>
                                    <li className={classes.listItem}><PersonIcon className={classes.adjustment}/>
                                        <strong>{entitydetail.registerAgent.fileAs}</strong></li>
                                    <li className={classes.listItem}><StreetviewIcon
                                        className={classes.adjustment}/> {entitydetail.registerAgent.address}
                                    </li>
                                    <li className={classes.listItem}><RoomIcon
                                        className={classes.adjustment}/>{entitydetail.registerAgent.address2}</li>
                                    <li className={classes.listItem}><LocationCityIcon
                                        className={classes.adjustment}/> {entitydetail.registerAgent.city}, {entitydetail.registerAgent.state} {entitydetail.registerAgent.zipcode}
                                    </li>
                                </ul> :
                                <ul className={classes.companyinfo}>
                                    <li className={classes.listItem}><PersonIcon className={classes.adjustment}/>
                                        <strong> - </strong></li>
                                    <li className={classes.listItem}><RoomIcon className={classes.adjustment}/> -
                                    </li>
                                    <li className={classes.listItem}><BusinessIcon className={classes.adjustment}/> -
                                    </li>
                                    <li className={classes.listItem}><LocationCityIcon
                                        className={classes.adjustment}/> -
                                    </li>
                                </ul>
                            }

                        </PortletBody>
                    </Portlet>
                </Grid>


                <Grid item xs={12} sm={4}>
                    <Portlet fluidHeight={true}>
                        <PortletHeader icon={<FastForwardIcon className={classes.adjustment}/>}
                                       title="Forwarding Address"/>
                        <PortletBody>
                            {entitydetail ?
                                <ul className={classes.companyinfo}>
                                    <li className={classes.listItem}><RoomIcon className={classes.adjustment}/>
                                        <strong>{entitydetail.entity.shippingStreet}, {entitydetail.entity.shippingCity}, {entitydetail.entity.shippingState} {entitydetail.entity.shippingCode} </strong>
                                    </li>
                                    <li className={classes.listItem}><MailIcon
                                        className={classes.adjustment}/> {entitydetail.entity.email}
                                    </li>
                                </ul> :
                                <ul className={classes.companyinfo}>
                                    <li className={classes.listItem}><RoomIcon className={classes.adjustment}/>
                                        <strong>-</strong></li>
                                    <li className={classes.listItem}><MailIcon
                                        className={classes.adjustment}/>-
                                    </li>
                                </ul>

                            }

                        </PortletBody>
                    </Portlet>
                </Grid>
            </Grid>
            <Grid container spacing={5}>

                <Grid item xs={12}>
                    <ContactList loading={loading} tooltip={'Add New Contact'} redirect={true}
                                 url={`/dashboard/contact/form/add/${entity_id}`} data={taskData}
                                 title={'Compliance Tasks'}/>
                </Grid>
            </Grid>

            <Grid container spacing={5}>
                <Grid item xs={12}>
                    <ContactList action={true} loading={loading} tooltip={'Add New Attachment'} redirect={true}
                                 url={`/dashboard/attachment/form/add/${entity_id}`} data={attachmentData}
                                 title={'Attachments'}/>
                </Grid>

            </Grid>
            <Grid container spacing={5}>
                <Grid item xs={12}>
                    <ContactList action={true} loading={loading} tooltip={'Add New Contact'} redirect={true}
                                 url={`/dashboard/contact/form/add/${entity_id}`} data={contactData}
                                 title={'Contacts'}/>
                </Grid>
            </Grid>

        </>
    )
}

export default withAuth(EntityDetailedPage);

