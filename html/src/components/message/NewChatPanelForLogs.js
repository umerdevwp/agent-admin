import React, {useContext, ReactDOM} from 'react';
import Avatar from "@material-ui/core/Avatar";
import {UserContext} from "../context/UserContext";
import {object} from "prop-types";
import Typography from "@material-ui/core/Typography";
import Select from "@material-ui/core/Select";
import {sendMessageFormChat} from '../api/message';
import clsx from "clsx";
// import {animateScroll} from "react-scroll";
import { ScrollTo } from "react-scroll-to";

import {toast} from "react-toastify";
import {animateScroll, Element, scroller} from "react-scroll";

const ReactDOMServer = require('react-dom/server');
const HtmlToReactParser = require('html-to-react').Parser;


const NewChatPanelForLogs = (props) => {
    const ref = React.createRef();
    const chatContainer = React.createRef();
    const entity_id = localStorage.getItem('activeEntityID');
    const IncomingIDFromAllMessages = localStorage.getItem('activeLogThread');
    const {attributes, loading, role, profile, userMessages, addMessage, manageOuterThreads, messageLogThreads} = useContext(UserContext);
    const [inComingThreadID, setInComingThreadID] = React.useState(IncomingIDFromAllMessages);
    const [activeThread, setActiveThread] = React.useState('');
    const [chosenThread, setChosenThread] = React.useState({});

    const [updateThread, setUpdateThread] = React.useState(false)

    const [message, setMessage] = React.useState('');

    React.useEffect(() => {
        const obj = messageLogThreads.find(o => o.id === inComingThreadID);
        setActiveThread(obj.id);
        setChosenThread(obj);

        setTimeout(() => {
            scrollToBottom(obj.id);
        }, 1000);

    }, [inComingThreadID]);


    const scrollToBottom = (id) => {
        let goToContainer = new Promise((resolve, reject) => {
            scroller.scrollTo(`containerElementThread${id}`,{
                containerId: `contacts`,
                duration: 0,
                delay: 0,
                smooth: "easeInOutQuart",
            });
            resolve();
        });
        const getMessageID = localStorage.getItem('activeLogMessage');
        // console.log('MessageID', getMessageID);
        let goToMessage = new Promise((resolve, reject) => {
            scroller.scrollTo(`containerElementMessage${getMessageID}`,{
                containerId: `containerElement`,
                duration: 0,
                delay: 0,
                smooth: "easeInOutQuart",
            });
            resolve();
        });


    }



    const onClickThread = async (threadID) => {
        const obj = messageLogThreads.find(o => o.id === threadID);
        await setChosenThread(obj);
        setMessage('');
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

        let form = new FormData();
        form.append('eid', '4071993000001842131')
        form.append('subject', 'RE: ' + chosenThread.subject);
        form.append('gid', chosenThread.id);
        form.append('message', message);
        const response = sendMessageFormChat(form).then(response => {
            if (response.status === true) {
                setMessage('')
                setUpdateThread(true);
                addMessage(chosenThread, message, role, attributes.organization)
            }


            if (response.status === false) {
                toast.error(response.message, {
                    position: toast.POSITION.BOTTOM_LEFT
                });
            }

        });

    }


    const stripHTML = (myString) => {
        return myString.replace(/<[^>]*>?/gm, '').replace(/\&nbsp;/g, '');
    }

    const truncate = (str, no_words) => {
        return str.split(" ").splice(0, no_words).join(" ") + " ...";
    }


    const convertHTML = (data) => {

        const htmlInput = data;
        const htmlToReactParser = new HtmlToReactParser();
        return htmlToReactParser.parse(htmlInput);

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
            return getInitials(localStorage.getItem('ThreadBelongsTo'))
        } else {
            return getInitials(localStorage.getItem('ThreadBelongsTo'))

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
                        {messageLogThreads.map((anObjectMapped, index) =>

                            <li id={`containerElementThread${anObjectMapped.id}`}
                                className={clsx('contact', chosenThread ? chosenThread.id === anObjectMapped.id ? 'active-thread' : '' : '')}
                                onClick={() => onClickThread(anObjectMapped.id)} key={anObjectMapped.id}>
                                <div className="wrap">
                                    {/*<Avatar alt="Remy Sharp" src="/static/images/avatar/1.jpg" />*/}
                                    {/*<img src="http://emilcarlsson.se/assets/louislitt.png" alt=""/>*/}

                                    <Avatar>{localStorage.getItem('ThreadBelongsTo') ? getInitials(localStorage.getItem('ThreadBelongsTo')) : ''}</Avatar>
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
                            <li id={`containerElementMessage${chosenThread.id}`} className={messageOrientation(chosenThread)}>
                                {/*<Avatar alt={anObjectMapped.user} />*/}
                                <Avatar>{namePicker(chosenThread)}</Avatar>
                                <div className="message-body-chat">
                                    <span><strong>    Subject: </strong> {chosenThread.subject}</span>
                                    {isHTML(chosenThread.message) === true ?
                                        convertHTML(chosenThread.message) :
                                        <p>{chosenThread.message}</p>}

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
                                <li id={`containerElementMessage${anObjectMapped.id}`}  key={index} className={messageOrientation(anObjectMapped)}>
                                    <Avatar>{namePicker(anObjectMapped)}</Avatar>
                                    <div className="message-body-chat">
                                        <span><strong>Subject: </strong> {anObjectMapped.subject}</span>
                                        {isHTML(anObjectMapped.message) === true ?
                                            convertHTML(anObjectMapped.message) :
                                            <p>{anObjectMapped.message}</p>}
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
                        <input disabled={true} onKeyPress={handleKeypress} value={message} type="text"
                               placeholder="Type a message"/>
                        <i className="fa fa-paperclip attachment" aria-hidden="true"></i>
                        <button disabled={true} className="submit"><i
                            className="fa fa-paper-plane"
                            aria-hidden="true"></i></button>
                    </div>
                </div>
            </div>
        </div>

    )
}

export default NewChatPanelForLogs;
