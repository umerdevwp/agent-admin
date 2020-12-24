import React, {useContext, ReactDOM} from 'react';
import Avatar from "@material-ui/core/Avatar";
import {UserContext} from "../context/UserContext";
import {object} from "prop-types";

const NewChatPanel = () => {
    const ref = React.createRef();
    const {attributes, loading, role, profile} = useContext(UserContext);
    const [inComingThreadID, setInComingThreadID] = React.useState('1');
    const [activeThread, setActiveThread] = React.useState('1');
    const [chosenThread, setChosenThread] = React.useState({});
    const [threads, setThreads] = React.useState([
        {
            id: '1',
            name: 'Louis Litt',
            member: 'You just got LITT up, Mike.',
            messages: [
                {
                    id: '1',
                    user: 'self',
                    message_type: 'sent',
                    message: 'How the hell am I supposed to get a jury to believe you when I am not even sure that I do?!',
                    subject:'Lorem ipsum isk ksieks',
                    sendTime: '2020-12-14 13:37:17',
                    status: 'delivered',

                },

                {
                    id: '2',
                    user: 'Omer Shafqat',
                    message_type: 'replies',
                    message: 'When you\'re backed against the wall, break the god damn thing down.',
                    subject:'Lorem ipsum isk ksieks',
                    sendTime: '2020-12-14 13:37:17',
                    status: 'delivered',

                }
            ]

        },

        {
            id: '2',
            name: 'Harvey Specter',
            member: 'You just got LITT up, Mike.',
            messages: [
                {
                    id: '1',
                    user: 'self',
                    message_type: 'sent',
                    message: 'This is the conversation number 2',
                    subject:'Lorem ipsum isk ksieks',
                    sendTime: '2020-12-14 13:37:17',
                    status: 'delivered',

                },

                {
                    id: '2',
                    user: 'Omer Shafqat',
                    message_type: 'replies',
                    message: 'When you\'re backed against the wall, break the god damn thing down.',
                    subject:'Lorem ipsum isk ksieks',
                    sendTime: '2020-12-14 13:37:17',
                    status: 'delivered',

                }
            ]
        },

        {
            id: '3',
            name: 'Rachel Zane',
            member: 'You just got LITT up, Mike.',
            messages: [
                {
                    id: '1',
                    user: 'self',
                    message_type: 'sent',
                    message: 'This is the conversation number 3',
                    subject:'Lorem ipsum isk ksieks',
                    sendTime: '2020-12-14 13:37:17',
                    status: 'delivered',

                },

                {
                    id: '2',
                    user: 'Omer Shafqat',
                    message_type: 'replies',
                    message: 'When you\'re backed against the wall, break the god damn thing down.',
                    subject:'Lorem ipsum isk ksieks',
                    sendTime: '2020-12-14 13:37:17',
                    status: 'delivered',

                }
            ]
        },

        {
            id: '4',
            name: 'Donna Paulsen',
            member: 'You just got LITT up, Mike.',
            messages: [
                {
                    id: '1',
                    user: 'self',
                    message_type: 'sent',
                    message: 'This is the conversation number 4',
                    subject:'Lorem ipsum isk ksieks',
                    sendTime: '2020-12-14 13:37:17',
                    status: 'delivered',

                },

                {
                    id: '2',
                    user: 'Omer Shafqat',
                    message_type: 'replies',
                    message: 'When you\'re backed against the wall, break the god damn thing down.',
                    subject:'Lorem ipsum isk ksieks',
                    sendTime: '2020-12-14 13:37:17',
                    status: 'delivered',

                }
            ]
        },


        {
            id: '5',
            name: 'Louis Litt',
            member: 'You just got LITT up, Mike.',
            messages: [
                {
                    id: '1',
                    user: 'self',
                    message_type: 'sent',
                    message: 'This is the conversation number 5',
                    subject:'Lorem ipsum isk ksieks',
                    sendTime: '2020-12-14 13:37:17',
                    status: 'delivered',

                },

                {
                    id: '2',
                    user: 'Omer Shafqat',
                    message_type: 'replies',
                    message: 'When you\'re backed against the wall, break the god damn thing down.',
                    subject:'Lorem ipsum isk ksieks',
                    sendTime: '2020-12-14 13:37:17',
                    status: 'delivered',

                }
            ]

        },

        {
            id: '6',
            name: 'Louis Litt',
            member: 'You just got LITT up, Mike.',
            messages: [
                {
                    id: '1',
                    user: 'self',
                    message_type: 'sent',
                    message: 'This is the conversation number 6',
                    subject:'Lorem ipsum isk ksieks',
                    sendTime: '2020-12-14 13:37:17',
                    status: 'delivered',

                },

                {
                    id: '2',
                    user: 'Omer Shafqat',
                    message_type: 'replies',
                    message: 'When you\'re backed against the wall, break the god damn thing down.',
                    subject:'Lorem ipsum isk ksieks',
                    sendTime: '2020-12-14 13:37:17',
                    status: 'delivered',

                }
            ]

        },

    ]);

    const [message, setMessage] = React.useState('');

    React.useEffect(() => {
        const obj = threads.find(o => o.id === inComingThreadID);
        setChosenThread(obj)
        setActiveThread(obj.id);
        setChosenThread(obj);
    }, [inComingThreadID]);


    React.useEffect(() => {
        if (Object.keys(chosenThread).length !== 0) {
            setActiveThread(chosenThread.id);
            setMessage('');
        }
    }, [chosenThread]);


    React.useEffect(() => {
        const obj = threads.find(o => o.id === activeThread);
        setChosenThread(obj);
    }, [threads])

    const onClickThread = async (threadID) => {
        const obj = threads.find(o => o.id === threadID);
        await setChosenThread(obj);
    }


    var getInitials = function (string) {
        var names = string.split(' '),
            initials = names[0].substring(0, 1).toUpperCase();

        if (names.length > 1) {
            initials += names[names.length - 1].substring(0, 1).toUpperCase();
        }
        return initials;
    };


    const sendMessage = (event) => {
        if (event.key === 'Enter') {

        } else {
            return false;
        }
        const id = Math.floor((Math.random() * 10) + 1);




        const elementsIndex = threads.findIndex(element => element.id === activeThread)
        const subjectaa = threads[elementsIndex].messages[0].subject;

        let newArray = [...threads];
        newArray[elementsIndex] = {
            ...newArray[elementsIndex], messages: [...newArray[elementsIndex].messages, {
                id: id.toString(),
                user: 'self',
                message_type: 'sent',
                sendTime: '2020-12-14 13:37:17',
                subject: subjectaa,
                message: message,
                status: 'delivered',
            }]
        }
        setThreads(newArray);
    }

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
                    <input type="text" placeholder="Search contacts..."/>
                </div>
                <div id="contacts">
                    <ul>
                        {threads?.map((anObjectMapped, index) =>
                            <li onClick={() => onClickThread(anObjectMapped.id)} key={index} className="contact">
                                <div className="wrap">
                                    {/*<Avatar alt="Remy Sharp" src="/static/images/avatar/1.jpg" />*/}
                                    {/*<img src="http://emilcarlsson.se/assets/louislitt.png" alt=""/>*/}
                                    <Avatar>{anObjectMapped.name ? getInitials(anObjectMapped.name) : ''}</Avatar>
                                    <div className="meta">
                                        <p className="name">{anObjectMapped.name}</p>
                                        <p className="preview">{anObjectMapped.member}</p>
                                    </div>
                                </div>
                            </li>
                        )}

                    </ul>
                </div>
            </div>
            <div className="content">

                <div className="messages">
                    <ul>
                        {chosenThread ?

                            chosenThread.messages?.map((anObjectMapped, index) =>
                                <li key={index} className={anObjectMapped.message_type}>
                                    {/*<Avatar alt={anObjectMapped.user} />*/}
                                    <Avatar>{anObjectMapped.user === 'self' ? getInitials(profile.name) : getInitials(anObjectMapped.user)}</Avatar>

                                    <p>
                                        <span><strong>    Subject -</strong> {anObjectMapped.subject}</span>
                                        {anObjectMapped.message}

                                        <div className="message-info" >
                                            <div className="message-time">
                                                <span>{anObjectMapped.sendTime}</span>
                                            </div>

                                            <div className="message-status">
                                                <span>{anObjectMapped.status}</span>
                                            </div>
                                        </div>
                                    </p>

                                </li>
                            )


                            : ''

                        }


                        {Object.keys(chosenThread).length === 0 ?
                            threads[0].messages?.map((anObjectMapped, index) =>
                                <li key={index} className={anObjectMapped.message_type}>
                                    {/*<Avatar alt={anObjectMapped.user} />*/}
                                    <Avatar>{anObjectMapped.user === 'self' ? getInitials(profile.name) : getInitials(anObjectMapped.user)}</Avatar>
                                    <p id={index}  ref={ref} ><span>Subject: {anObjectMapped.subject}</span>{anObjectMapped.message}
                                        <div className="message-info" >
                                            <div className="message-time">
                                                <span>{anObjectMapped.sendTime}</span>
                                            </div>

                                            <div className="message-status">
                                                <span>{anObjectMapped.status}</span>
                                            </div>
                                        </div>
                                    </p>



                                </li>
                            ) : ''
                        }


                    </ul>
                </div>
                <div className="message-input">
                    <div className="wrap">
                        <input onKeyDown={(event) => {sendMessage(event)}} value={message} onChange={(event) => setMessage(event.target.value)} type="text"
                               placeholder="Type a message"/>
                        <i className="fa fa-paperclip attachment" aria-hidden="true"></i>
                        <button onClick={() => sendMessage()} className="submit"><i className="fa fa-paper-plane"
                                                                                    aria-hidden="true"></i></button>
                    </div>
                </div>
            </div>
        </div>
    )
}

export default NewChatPanel;
