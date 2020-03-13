import React, {useMemo} from "react";
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

import PictureAsPdfIcon from '@material-ui/icons/PictureAsPdf';

import BusinessIcon from '@material-ui/icons/Business';
import MailIcon from '@material-ui/icons/Mail';
import Container from "@material-ui/core/Container";
import Link from "@material-ui/core/Link";
import {useHistory} from "react-router-dom";

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
    const classes = useStyles();
    const history = useHistory();
    const contactData = {
        columns: [
            {title: 'Name', field: 'full_name'},
            {title: 'Contact Type', field: 'title'},
            {title: 'email', field: 'email'},
            {title: 'Street', field: 'mailing_street'},
            {title: 'City', field: 'mailing_city'},
            {title: 'State', field: 'mailing_state'},
            {title: 'Phone', field: 'phone'},
        ],
        data: [
            {id: 1, full_name: 'David', title: 'CFO', email: 'omer@gmail.com', mailing_street: 'Abc Dr, Beloit WI', mailing_city: 'Beloit', mailing_state:'Wisconsin - WI', phone: '03335614017'},
            {id: 1, full_name: 'David Off', title: 'CFO', email: 'omer@gmail.com', mailing_street: 'Abc Dr, Beloit WI', mailing_city: 'Beloit', mailing_state:'Wisconsin - WI', phone: '03335614017'},
            {id: 1, full_name: 'David Test', title: 'CFO', email: 'omer@gmail.com', mailing_street: 'Abc Dr, Beloit WI', mailing_city: 'Beloit', mailing_state:'Wisconsin - WI', phone: '03335614017'},
            {id: 1, full_name: 'David', title: 'CFO', email: 'omer@gmail.com', mailing_street: 'Abc Dr, Beloit WI', mailing_city: 'Beloit', mailing_state:'Wisconsin - WI', phone: '03335614017'},
            {id: 1, full_name: 'David', title: 'CFO', email: 'omer@gmail.com', mailing_street: 'Abc Dr, Beloit WI', mailing_city: 'Beloit', mailing_state:'Wisconsin - WI', phone: '03335614017'},
            {id: 1, full_name: 'David', title: 'CFO', email: 'omer@gmail.com', mailing_street: 'Abc Dr, Beloit WI', mailing_city: 'Beloit', mailing_state:'Wisconsin - WI', phone: '03335614017'},
            {id: 1, full_name: 'David', title: 'CFO', email: 'omer@gmail.com', mailing_street: 'Abc Dr, Beloit WI', mailing_city: 'Beloit', mailing_state:'Wisconsin - WI', phone: '03335614017'},
            {id: 1, full_name: 'David', title: 'CFO', email: 'omer@gmail.com', mailing_street: 'Abc Dr, Beloit WI', mailing_city: 'Beloit', mailing_state:'Wisconsin - WI', phone: '03335614017'},
            {id: 1, full_name: 'David', title: 'CFO', email: 'omer@gmail.com', mailing_street: 'Abc Dr, Beloit WI', mailing_city: 'Beloit', mailing_state:'Wisconsin - WI', phone: '03335614017'},
            {id: 1, full_name: 'David', title: 'CFO', email: 'omer@gmail.com', mailing_street: 'Abc Dr, Beloit WI', mailing_city: 'Beloit', mailing_state:'Wisconsin - WI', phone: '03335614017'},


        ],
    };


    const attachmentData  = {
        columns: [
            {
                title: 'File Name',
                editable: 'never',
                render: rowData => rowData ? (<Link
                    component="button"
                    variant="body2"
                    onClick={() => {
                        history.push(`/dashboard/entity/${rowData.id}`);
                    }}>
                    <PictureAsPdfIcon/> {rowData.file_name}
                </Link>) : 'Sample.pdf'
            },
            {title: 'Attached By', field: 'attachment_by'},
            {title: 'Date Added', field: 'date_added'},
            {title: 'Size', field: 'size', editable: 'never'},
        ],
        data: [
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},


        ],
    };



    const {brandColor, dangerColor, successColor, primaryColor} = useSelector(
        state => ({
            brandColor: metronic.builder.selectors.getConfig(
                state,
                "colors.state.brand"
            ),
            dangerColor: metronic.builder.selectors.getConfig(
                state,
                "colors.state.danger"
            ),
            successColor: metronic.builder.selectors.getConfig(
                state,
                "colors.state.success"
            ),
            primaryColor: metronic.builder.selectors.getConfig(
                state,
                "colors.state.primary"
            )
        })
    );

    const chartOptions = useMemo(
        () => ({
            chart1: {
                data: [10, 14, 18, 11, 9, 12, 14, 17, 18, 14],
                color: brandColor,
                border: 3
            },

            chart2: {
                data: [11, 12, 18, 13, 11, 12, 15, 13, 19, 15],
                color: dangerColor,
                border: 3
            },

            chart3: {
                data: [12, 12, 18, 11, 15, 12, 13, 16, 11, 18],
                color: successColor,
                border: 3
            },

            chart4: {
                data: [11, 9, 13, 18, 13, 15, 14, 13, 18, 15],
                color: primaryColor,
                border: 3
            }

        }),
        [brandColor, dangerColor, primaryColor, successColor]
    );
    return (

        <>
            <Title title={'Capitol Office Inc.'}/>

            <Grid container spacing={1}>
                <Grid item xs={12} sm={4}>
                    <Portlet fluidHeight={true}>
                        <PortletHeader icon={<PermIdentityIcon className={classes.adjustment}/>} title="Company Info"/>
                        <PortletBody>
                            <ul className={classes.companyinfo}>
                                <li className={classes.listItem}><strong>State ID:</strong> 0</li>
                                <li className={classes.listItem}><strong>Formation Date:</strong> 2019-12-06</li>
                                <li className={classes.listItem}><strong>Expiration Date: </strong> 2019-12-06</li>
                                <li className={classes.listItem}><strong>Tax ID:</strong> 09890890</li>
                            </ul>
                        </PortletBody>
                    </Portlet>
                </Grid>
                <Grid item xs={12} sm={4}>
                    <Portlet fluidHeight={true}>
                        <PortletHeader icon={<RoomIcon className={classes.adjustment}/>} title="RA Address"/>
                        <PortletBody>
                            <ul className={classes.companyinfo}>
                                <li className={classes.listItem}><PersonIcon className={classes.adjustment}/> <strong>Legalinc
                                    Corporate Services Inc</strong></li>
                                <li className={classes.listItem}><RoomIcon className={classes.adjustment}/> 1810 E
                                    Sahara Ave STE 215
                                </li>
                                <li className={classes.listItem}><BusinessIcon className={classes.adjustment}/> Las
                                    Vegas, NV 89104
                                </li>
                            </ul>
                        </PortletBody>
                    </Portlet>
                </Grid>
                <Grid item xs={12} sm={4}>
                    <Portlet fluidHeight={true}>
                        <PortletHeader icon={<FastForwardIcon className={classes.adjustment}/>}
                                       title="Forwarding Address"/>
                        <PortletBody>
                            <ul className={classes.companyinfo}>
                                <li className={classes.listItem}><RoomIcon className={classes.adjustment}/> <strong>1595
                                    Cattle Ranch Place Henderson, NV 89002</strong></li>
                                <li className={classes.listItem}><MailIcon
                                    className={classes.adjustment}/> omer.s@allshorestaffing.com
                                </li>
                            </ul>
                        </PortletBody>
                    </Portlet>
                </Grid>
            </Grid>
            <Grid container spacing={1}>
                <Grid item xs={12}>
                    <Portlet fluidHeight={true}>
                        <PortletHeader icon={<ViewListIcon className={classes.adjustment}/>}
                                       title="Compliance Check List"/>
                        <PortletBody>
                            <EntityListing title={'Compliance Tasks'}/>
                        </PortletBody>
                    </Portlet>
                </Grid>
            </Grid>

            <Grid container spacing={1}>
                <Grid item xs={12}>
                    <Portlet fluidHeight={true}>
                        <PortletHeader icon={<AttachmentIcon className={classes.adjustment}/>} title="Attachments"/>
                        <PortletBody>
                            <EntityListing tooltip={'Add File'} redirect={true} url={'/dashboard/contact/form/add'} data={attachmentData} title={''}/>
                        </PortletBody>
                    </Portlet>
                </Grid>
            </Grid>

            <Grid container spacing={1}>
                <Grid item xs={12}>
                    <Portlet fluidHeight={true}>
                        <PortletHeader icon={<ContactsIcon className={classes.adjustment}/>} title="Contacts"/>
                        <PortletBody>
                            <EntityListing tooltip={'Add New Contact'} redirect={true} url={'/dashboard/contact/form/add'} data={contactData} title={''}/>
                        </PortletBody>
                    </Portlet>
                </Grid>
            </Grid>

        </>
    )
}

export default withAuth(EntityDetailedPage);

