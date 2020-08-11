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
import Breadcrumbs from '@material-ui/core/Breadcrumbs';
import Typography from '@material-ui/core/Typography';
import MaterialTable from 'material-table';
import {OktaUserContext} from "../../context/OktaUserContext";
import {AttachmentsList} from "../../crud/attachment";
import ContactList from "../entity/ContactList";

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


const Attachments = () => {
    const {oktaprofile, isAdmin} = useContext(OktaUserContext);
    const history = useHistory();
    const classes = useStyles();
    const [attachmentList, setattachmentList] = React.useState([])
    const [loading, setLoading] = React.useState(true)
    useEffect(() => {
        fetchAttachmentData();
    }, []);

    const fetchAttachmentData = async () => {
        try {
            const response = await AttachmentsList(oktaprofile.organization);
            await setattachmentList(response.data.attachments);
            setLoading(false);

        } catch (e) {
            console.log(e);
        }
    }

    const dummyData = {
        columns: [
            {
                title: 'File Name',
                editable: 'never',
                render: rowData => <a target="_blank" href={`${process.env.REACT_APP_SERVER_API_URL}/download/file/${rowData.fid}?name=${rowData.name}`}> <PictureAsPdfIcon/> {rowData.name}
                </a>
            },
            {title: 'Date', field: 'created'},
            {title: 'Size', field: 'fileSize'},
        ],
        data: attachmentList,
    };


    const Additem = (event) => {
        console.log('lulu');
    }


    return (
        <>


            <Title title={'Attachments'}/>
            <Grid container spacing={1}>
                <Grid item xs={12}>
                    <Portlet fluidHeight={true}>
                        <PortletHeader icon={<AttachmentIcon className={classes.adjustment}/>} title="Attachment List"/>
                        <PortletBody>
                            <ContactList loading={loading} tooltip={'Add Attachment'} redirect={true}
                                         url={'/dashboard/attachment/form/add'} click={Additem} data={dummyData}
                                         title={''}/>
                        </PortletBody>
                    </Portlet>
                </Grid>
            </Grid>
        </>
    )
}

export default Attachments;
