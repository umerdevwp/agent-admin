import React, {useContext, useEffect, useState} from 'react';
import {makeStyles} from '@material-ui/core/styles';
import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';
import Divider from '@material-ui/core/Divider';
import ListItemText from '@material-ui/core/ListItemText';
import ListItemAvatar from '@material-ui/core/ListItemAvatar';
import Avatar from '@material-ui/core/Avatar';
import Typography from '@material-ui/core/Typography';
import {FetchThreads} from '../api/message';
import {UserContext} from "../context/UserContext";
import Select from "@material-ui/core/Select";
import Button from "@material-ui/core/Button";
import moment from "moment";

const useStyles = makeStyles((theme) => ({
    root: {
        width: '100%',
        // maxWidth: '36ch',
        backgroundColor: theme.palette.background.paper,
    },
    inline: {
        display: 'inline',
    },

    viewButton: {
        width: '100%',
        textAlign: 'center'
    }

}));

export default function AllMessages(props) {
    const classes = useStyles();
    const {loading, addError, errorList, role, setUserMessagesForInbox, outerThreads, manageOuterThreads} = useContext(UserContext);
    const [threads, setThreads] = useState([]);
    useEffect(() => {
        if (loading === true) {
            getAllMessageThreads()
        }
    }, [loading]);

    useEffect(() => {
        if (outerThreads === true) {
            getAllMessageThreads()
            manageOuterThreads(false);
        }

    }, [outerThreads]);

    const getAllMessageThreads = async () => {
        FetchThreads().then(response => {
            if (response.status === true) {
                setUserMessagesForInbox(response.data);
                setThreads(response.data);
            }
        });
    }


    const stripHTML = (myString) => {
        return myString.replace(/<[^>]*>?/gm, '');
    }

    const truncate = (str, no_words) => {
        return str.split(" ").splice(0, no_words).join(" ") + " ...";
    }

    const handleClickChange = (id) => {
        localStorage.setItem('allMessagesThread', id);
        props.openmodal();
        // localStorage.setItem('allMessagesFirstThread', id);
    }


    const ViewAllHandleClickChange = () => {
        if(threads[0].id) {
            localStorage.setItem('allMessagesThread', threads[0].id);
        }
        props.openmodal();
        // localStorage.setItem('allMessagesFirstThread', id);
    }

    const lastMessage = (threadInfo) => {

        if ((threadInfo.child).length !== 0) {

            if ((threadInfo.child).length > 0) {
                const data = threadInfo.child;
                const lastChild = data.slice(-1)[0]
                return truncate(stripHTML(lastChild.message), 10);

            } else {
                return truncate(stripHTML(threadInfo.child[0].message), 10)
            }
            // console.log(threadInfo.child.last())
            // return threadInfo.child.last();
        } else {
            return truncate(stripHTML(threadInfo.message), 10);
        }
    }

    return (
        <>
            <List className={classes.root}>
                {threads?.slice(0, 5).map((anObjectMapped, index) =>
                    <div key={index}>
                        <ListItem onClick={(e) => handleClickChange(anObjectMapped.id)} alignItems="flex-start">
                            <ListItemAvatar>
                                <Avatar alt="Remy Sharp" src="/static/images/avatar/1.jpg"/>
                            </ListItemAvatar>
                            <ListItemText
                                primary={anObjectMapped.subject}
                                secondary={
                                    <React.Fragment>
                                        <Typography
                                            component="span"
                                            variant="body2"
                                            className={classes.inline}
                                            color="textPrimary"
                                        >
                                            {props.entityName}
                                        </Typography>
                                        {` — ${lastMessage(anObjectMapped)}`}
                                    </React.Fragment>
                                }
                            />
                        </ListItem>
                        <Divider variant="inset" component="li"/>
                    </div>
                )
                }
            </List>
            <div className={classes.viewButton}>
                <Button onClick={ViewAllHandleClickChange} variant="outlined" color="primary">View All</Button>
            </div>
        </>
    )
}
