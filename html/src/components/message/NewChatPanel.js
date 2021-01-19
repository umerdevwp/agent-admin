import React, {useContext, ReactDOM} from 'react';
import Avatar from "@material-ui/core/Avatar";
import {UserContext} from "../context/UserContext";
import {object} from "prop-types";
import Typography from "@material-ui/core/Typography";
import Select from "@material-ui/core/Select";
import {sendMessageFormChat} from '../api/message';
import clsx from "clsx";
import Button from "@material-ui/core/Button";
import {
    Link,
    DirectLink,
    Element,
    Events,
    animateScroll,
    scrollSpy,
    scroller
} from "react-scroll";
import {toast} from "react-toastify";
import stripHtml from "string-strip-html";
import Chip from '@material-ui/core/Chip';

import PictureAsPdfIcon from '@material-ui/icons/PictureAsPdf';
import List from "@material-ui/core/List";
import ListItem from "@material-ui/core/ListItem";
import ListItemAvatar from "@material-ui/core/ListItemAvatar";
import ListItemText from "@material-ui/core/ListItemText";
import ListItemSecondaryAction from "@material-ui/core/ListItemSecondaryAction";
import IconButton from "@material-ui/core/IconButton";
import DeleteIcon from "@material-ui/icons/Delete";
import CloudDownloadIcon from '@material-ui/icons/CloudDownload';
import SendIcon from '@material-ui/icons/Send';
import CircularProgress from '@material-ui/core/CircularProgress';

const ReactDOMServer = require('react-dom/server');
const HtmlToReactParser = require('html-to-react').Parser;


const NewChatPanel = (props) => {
    const ref = React.createRef();
    // const inputRef = React.useRef();
    const chatContainer = React.createRef();
    const entity_id = localStorage.getItem('activeEntityID');
    const IncomingIDFromAllMessages = localStorage.getItem('allMessagesThread');
    const {attributes, loading, role, profile, userMessages, addMessage, manageOuterThreads} = useContext(UserContext);
    const [inComingThreadID, setInComingThreadID] = React.useState(IncomingIDFromAllMessages);
    const [activeThread, setActiveThread] = React.useState('');
    const [chosenThread, setChosenThread] = React.useState({});
    const [updateThread, setUpdateThread] = React.useState(false)
    const [message, setMessage] = React.useState('');
    const [file, setFile] = React.useState([]);
    const [key, setKey] = React.useState(0);
    const [componentLoading, setComponentLoading] = React.useState(false)

    React.useEffect(() => {
        const obj = userMessages.find(o => o.id === inComingThreadID);
        setActiveThread(obj.id);
        setChosenThread(obj);

        setTimeout(() => {
            scrollToBottom();
        }, 1000);

    }, [inComingThreadID]);


    React.useEffect(() => {
        if (updateThread) {
            manageOuterThreads(true);
            setUpdateThread(false);
            const obj = userMessages.find(o => o.id === chosenThread.id);
            setChosenThread(obj);
            setTimeout(() => {
                scrollToBottom();
            }, 2000);
        }

    }, [userMessages]);


    const scrollToBottom = () => {
        animateScroll.scrollToBottom({

            containerId: "containerElement",
            duration: 0,
            delay: 0,
            smooth: "easeInOutQuart",
        });
    }

    // React.useEffect(() => {
    //     const obj = userMessages.find(o => o.id === activeThread);
    //     setChosenThread(obj);
    // }, [threads])

    const onClickThread = async (threadID) => {
        const obj = userMessages.find(o => o.id === threadID);
        await setChosenThread(obj);
        setMessage('');
        scrollToBottom();
    }


    var getInitials = function (string) {
        var names = string.split(' '),
            initials = names[0].substring(0, 1).toUpperCase();

        if (names.length > 1) {
            initials += names[names.length - 1].substring(0, 1).toUpperCase();
        }
        return initials;
    };


    const handleKeypress = (event) => {
        if (event.key === 'Enter') {
            sendMessage();
        } else {
            return false;
        }
    }


    const sendMessage = (event) => {
        // event.preventDefault();
        setComponentLoading(true);
        let form = new FormData();
        const eid = localStorage.getItem('activeEntityID');
        form.append('eid', eid);
        form.append('subject', 'RE: ' + chosenThread.subject);
        form.append('gid', chosenThread.id);
        form.append('message', message);
        file.map(function (val, index) {
            form.append('attachment[]', val)
        })

        const response = sendMessageFormChat(form).then(response => {
            if (response.status === true) {
                setComponentLoading(false);
                setMessage('')
                setUpdateThread(true);
                setFile([]);
                addMessage(chosenThread, message, role, attributes.organization)
            }


            if (response.status === false) {
                setComponentLoading(false);
                if(response.error.message) {
                    toast.error(response.error.message, {
                        position: toast.POSITION.BOTTOM_LEFT
                    });
                }

                if(response.message) {
                    toast.error(response.message, {
                        position: toast.POSITION.BOTTOM_LEFT
                    });
                }
            }

        });

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


    const convertHTML = (data) => {

        // let html = data;
        // let reg = html.getElementsByTagName('body')[0].innerHTML()
        // let contentNew = html.match( reg )[1];


        if (typeof data !== 'undefined') {
            // console.log('Messages', data);
            // var newHTML = stripHtml(data, {
            //     stripTogetherWithTheirContents: [
            //         "table", // default
            //
            //     ],
            // }).result;

            const htmlInput = data;
            const htmlToReactParser = new HtmlToReactParser();
            const abc = htmlToReactParser.parse(htmlInput);
            return abc;
        } else {
            // console.log(typeof data)
            return data;

        }

    }


    const messageOrientation = (threadInfo) => {
        if (role === 'Administrator') {
            if (threadInfo.fromEid === '0') {
                return 'replies'
            } else {
                return 'sent'
            }
        }

        if (role === 'Child Entity' || role === 'Parent Organization') {
            // if (threadInfo.fromEid === profile.organization) {
            //     return 'sent'
            // } else {
            //     return 'replies'
            // }


            if (threadInfo.fromEid === '0') {
                return 'sent'
            }
            if (threadInfo.fromEid === attributes.organization) {
                return 'replies'
            }
        }

    }


    const namePicker = (threadInfo) => {
        if (threadInfo.fromEid === '0') {
            return getInitials('Admin')
        }

        if (threadInfo.fromEid === attributes.organization) {
            return getInitials(localStorage.getItem('entityName'))
        } else {
            return getInitials(localStorage.getItem('entityName'))

        }
    }


    const lastMessage = (threadInfo) => {

        if ((threadInfo.child).length !== 0) {

            if ((threadInfo.child).length > 0) {
                const data = threadInfo.child;
                const lastChild = data.slice(-1)[0]
                return stripHTML(lastChild.message);
            } else {
                return stripHTML(threadInfo.child[0].message)
            }
            // console.log(threadInfo.child.last())
            // return threadInfo.child.last();
        } else {
            return stripHTML(threadInfo.message);
        }
    }

    const bytesToSize = (bytes) => {
        let sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        if (bytes == 0) return '0 Byte';
        let i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
        return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
    }


    const handleDelete = (index) => {
        let array = [...file];
        if (index > -1) {
            array.splice(index, 1);
        }
        setFile(array);
        setKey(Math.floor((Math.random() * 10) + 1));
    };

    const attachFile = (e) => {
        const incomingFile = e.target.files;
        let array = [];
        for (let i = 0; i < incomingFile.length; i++) {
            array.push(incomingFile[i]);
        }
        setFile(file.concat(array));
    }

    const isHTML = (str) => !(str || '')
        // replace html tag with content
        .replace(/<([^>]+?)([^>]*?)>(.*?)<\/\1>/ig, '')
        // remove remaining self closing tags
        .replace(/(<([^>]+)>)/ig, '')
        // remove extra space at start and end
        .trim();

    return (
        <div id="frame">
            <div id="sidepanel">
                <div id="profile">
                    <div className="wrap">
                        {/*<img id="profile-img" src="http://emilcarlsson.se/assets/mikeross.png" className="online"*/}
                        {/*     alt=""/>*/}
                        <Avatar>{profile.name ? getInitials(profile.name) : ''}</Avatar>
                        {profile ? <p>{profile.name}</p> : ''}
                    </div>
                </div>
                <div id="search">
                    <label htmlFor=""><i className="fa fa-search" aria-hidden="true"></i></label>
                    <input disabled type="text" placeholder="Search contacts..."/>
                </div>
                <div id="contacts">
                    <ul>
                        {userMessages.map((anObjectMapped, index) =>
                            <li className={clsx('contact', chosenThread ? chosenThread.id === anObjectMapped.id ? 'active-thread' : '' : '')}
                                onClick={() => onClickThread(anObjectMapped.id)} key={anObjectMapped.id}>
                                <div className="wrap">
                                    {/*<Avatar alt="Remy Sharp" src="/static/images/avatar/1.jpg" />*/}
                                    {/*<img src="http://emilcarlsson.se/assets/louislitt.png" alt=""/>*/}
                                    <Avatar>{localStorage.getItem('entityName') ? getInitials(localStorage.getItem('entityName')) : ''}</Avatar>
                                    <div className="meta">
                                        <p className="name">{anObjectMapped.subject}</p>
                                        <p className="preview">{lastMessage(anObjectMapped)}</p>
                                    </div>
                                </div>
                            </li>
                        )}

                    </ul>
                </div>
            </div>
            <div className="content">

                <div className="messages" id="containerElement">
                    <ul>

                        {chosenThread ?
                            <li className={messageOrientation(chosenThread)}>
                                {/*<Avatar alt={anObjectMapped.user} />*/}
                                <Avatar>{namePicker(chosenThread)}</Avatar>
                                <div className="message-body-chat">
                                    <span><strong>    Subject: </strong> {chosenThread.subject}</span>
                                    {isHTML(chosenThread.message) === false ?
                                        <>
                                            {convertHTML(chosenThread.message)}
                                            <List>
                                                {chosenThread.attachments?.map((anObjectMapped, index) =>
                                                    <ListItem key={index}>
                                                        <ListItemAvatar>
                                                            <Avatar>
                                                                <PictureAsPdfIcon/>
                                                            </Avatar>
                                                        </ListItemAvatar>
                                                        <ListItemText

                                                            primary={anObjectMapped.name}
                                                            secondary={anObjectMapped.size}
                                                        />
                                                        <ListItemSecondaryAction>
                                                            <IconButton edge="end" aria-label="delete">
                                                                <a download
                                                                   href={`/${anObjectMapped.path}`}><CloudDownloadIcon/></a>
                                                            </IconButton>
                                                        </ListItemSecondaryAction>
                                                    </ListItem>
                                                )}


                                            </List>

                                        </>
                                        :
                                        <p>{chosenThread.message}</p>

                                    }

                                    <div className="message-info">
                                        <div className="message-time">
                                            <span>{chosenThread.sendTime}</span>
                                        </div>

                                        <div className="message-status">
                                            {role === 'Administrator' ?
                                                <span>{chosenThread.status}</span> : ''}
                                        </div>
                                    </div>
                                </div>

                            </li> : ''}

                        {
                            chosenThread.child?.map((anObjectMapped, index) =>
                                <li key={index} className={messageOrientation(anObjectMapped)}>
                                    <Avatar>{namePicker(anObjectMapped)}</Avatar>
                                    <div className="message-body-chat">
                                        <span><strong>Subject: </strong> {anObjectMapped.subject}</span>
                                        <>
                                            {isHTML(anObjectMapped.message) === true ?
                                                convertHTML(anObjectMapped.message) :
                                                <p>{convertHTML(anObjectMapped.message)}</p>

                                            }
                                            <List>
                                                {anObjectMapped.attachments?.map((anObjectMapped, index) =>
                                                    <ListItem key={index}>
                                                        <ListItemAvatar>
                                                            <Avatar>
                                                                <PictureAsPdfIcon/>
                                                            </Avatar>
                                                        </ListItemAvatar>
                                                        <ListItemText

                                                            primary={anObjectMapped.name}
                                                            secondary={anObjectMapped.size}
                                                        />
                                                        <ListItemSecondaryAction>
                                                            <IconButton edge="end" aria-label="delete">
                                                                <a download
                                                                   href={`${process.env.REACT_APP_SERVER_API_URL}/${anObjectMapped.path}`}><CloudDownloadIcon/></a>
                                                            </IconButton>
                                                        </ListItemSecondaryAction>
                                                    </ListItem>
                                                )}


                                            </List>
                                        </>
                                        <div className="message-info">
                                            <div className="message-time">
                                                <span>{anObjectMapped.sendTime}</span>
                                            </div>

                                            <div className="message-status">
                                                {role === 'Administrator' ?
                                                    <span>{chosenThread.status}</span> : ''}
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            )}

                    </ul>
                </div>
                <div className="message-input">
                    <div className="wrap">
                        {file.length !== 0 ?
                            <div className="fileAttachmentInput">
                                {file?.map((anObjectMapped, index) =>
                                    <Chip
                                        key={index}
                                        icon={<PictureAsPdfIcon/>}
                                        label={anObjectMapped.name + ' - ' + bytesToSize(anObjectMapped.size)}
                                        clickable
                                        variant="outlined"
                                        color={'secondary'}
                                        onDelete={(e) => handleDelete(index)}
                                    />
                                )}
                            </div> : ''}

                        <input onKeyPress={handleKeypress} value={message}
                               onChange={(event) => setMessage(event.target.value)} type="text"
                               placeholder="Type a message"/>


                        <div className="attachmentModal">
                            <Button
                                variant="contained"
                                component="label"
                            >
                                <i className="fa fa-paperclip attachment" aria-hidden="true"></i>
                                <input
                                    multiple="multiple"
                                    accept="application/pdf"
                                    // key={key}
                                    type="file"
                                    hidden
                                    onChange={attachFile}
                                />
                            </Button>
                        </div>

                        <button onClick={(e) => sendMessage(e)} className="submit">{componentLoading === false ? <SendIcon/> :    <CircularProgress />}</button>
                    </div>
                </div>
            </div>
        </div>
    )
}

export default NewChatPanel;
