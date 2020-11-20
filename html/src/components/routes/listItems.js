import React from 'react';
import ListItem from '@material-ui/core/ListItem';
import ListItemIcon from '@material-ui/core/ListItemIcon';
import ListItemText from '@material-ui/core/ListItemText';
import DashboardIcon from '@material-ui/icons/Dashboard';
import PeopleIcon from '@material-ui/icons/People';
import Attachment from '@material-ui/icons/Attachment';
import {Link} from 'react-router-dom';
import {makeStyles} from '@material-ui/core/styles';
import ContactPhoneOutlinedIcon from '@material-ui/icons/ContactPhoneOutlined';

const useStyles = makeStyles(() => ({
    link: {
        color: 'white',
        textDecoration: 'none'


    }

}))

export default function MainListItems() {

    const classes = useStyles();
    return (
        <div>
            <Link className={classes.link} to='/'>
                <ListItem button>
                    <ListItemIcon>
                        <DashboardIcon color="primary"/>
                    </ListItemIcon>
                    <ListItemText primary="Dashboard"/>
                </ListItem>
            </Link>
            {/*<Link className={classes.link} to='/entity/new/add'>*/}
            {/*    <ListItem button>*/}
            {/*        <ListItemIcon>*/}
            {/*            <PeopleIcon color="primary"/>*/}
            {/*        </ListItemIcon>*/}
            {/*        <ListItemText primary="Add entity"/>*/}
            {/*    </ListItem>*/}
            {/*</Link>*/}
            <Link className={classes.link} to='/attachments'>
                <ListItem button>
                    <ListItemIcon>
                        <Attachment color="primary"/>
                    </ListItemIcon>
                    <ListItemText primary="Attachments"/>
                </ListItem>
            </Link>

            <Link className={classes.link} to='/admin/attachments'>
                <ListItem button>
                    <ListItemIcon>
                        <Attachment color="primary"/>
                    </ListItemIcon>
                    <ListItemText primary="Bulk Attachments"/>
                </ListItem>
            </Link>

            <Link className={classes.link} to='/contacts'>
                <ListItem button>
                    <ListItemIcon>
                        <ContactPhoneOutlinedIcon color="primary"/>
                    </ListItemIcon>
                    <ListItemText primary="Contacts"/>
                </ListItem>
            </Link>

        </div>)
};
