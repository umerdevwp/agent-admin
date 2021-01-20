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
import Button from "@material-ui/core/Button";
import Skeleton from '@material-ui/lab/Skeleton';
import ListItemSecondaryAction from "@material-ui/core/ListItemSecondaryAction";
import IconButton from "@material-ui/core/IconButton";
import CloudDownloadIcon from "@material-ui/icons/CloudDownload";

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
    },

    SkeletonLoading: {
        width: '100%'
    },

    skeletonWidth: {
        width: '100%'
    },

    skeletonStyle: {
        paddingLeft: theme.spacing(3),
        paddingRight: theme.spacing(3),
    },


}));

export default function AllMessages(props) {
    const classes = useStyles();
    const {loading, addError, errorList, role, setUserMessagesForInbox, outerThreads, manageOuterThreads} = useContext(UserContext);
    const [threads, setThreads] = useState([]);
    const [apiLoading, setApiLoading] = useState(false);
    const entity_id = localStorage.getItem('activeEntityID');
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
        setApiLoading(true);
        try {
            FetchThreads(entity_id).then(response => {
                if (response.status === true) {
                    setUserMessagesForInbox(response.data);
                    setThreads(response.data);
                    setApiLoading(false);
                }
            });
        } catch (e) {

            addError('Something went wrong with the message API')
        }
    }


    const stripHTML = (myString) => {
        if (myString) {
            return myString.replace(/<[^>]*>?/gm, '').replace(/\&nbsp;/g, '');
        } else {
            return myString;
        }
    }

    const truncate = (str, no_words) => {
        if (str) {
            return str.split(" ").splice(0, no_words).join(" ") + " ...";
        } else {
            return str;
        }
    }

    const handleClickChange = (id) => {
        localStorage.setItem('allMessagesThread', id);
        props.openmodal();
        // localStorage.setItem('allMessagesFirstThread', id);
    }


    const ViewAllHandleClickChange = () => {
        if (threads[0].id) {
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
            {!apiLoading ?
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

                                        {/*{*/}
                                        {/*    threads.attachments.length !== 0 ?*/}
                                        {/*        <ListItemSecondaryAction>*/}
                                        {/*            <IconButton edge="end" aria-label="delete">*/}
                                        {/*                <a download*/}
                                        {/*                   href='#'><CloudDownloadIcon/></a>*/}
                                        {/*            </IconButton>*/}
                                        {/*        </ListItemSecondaryAction>*/}
                                        {/*        : ''*/}
                                        {/*}*/}

                                </ListItem>

                                <Divider variant="inset" component="li"/>
                            </div>
                        )
                        }
                    </List>
                    {(threads.length === 0) ?
                        <>
                            <ListItem alignItems="flex-start">
                                <ListItemText
                                    primary={'No message found'}
                                    secondary={
                                        <React.Fragment>
                                            <Typography
                                                component="span"
                                                variant="body2"
                                                className={classes.inline}
                                                color="textPrimary"
                                            >
                                                Currently there are no active threads
                                            </Typography>
                                        </React.Fragment>
                                    }
                                />
                                <ListItemSecondaryAction>
                                    <IconButton edge="end" aria-label="delete">
                                        <a download
                                           href='#'><CloudDownloadIcon/></a>
                                    </IconButton>
                                </ListItemSecondaryAction>
                            </ListItem>
                        </>
                        : ''}


                    {(threads.length !== 0) ?
                        <div className={classes.viewButton}>
                            <Button onClick={ViewAllHandleClickChange} variant="outlined" color="primary">View
                                All</Button>
                        </div>
                        : ' '}
                </>
                :
                <>
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

                </>

            }
        </>)
}
