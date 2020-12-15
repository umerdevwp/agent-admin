import React, {useContext, useState} from 'react';
import TextField from '@material-ui/core/TextField';
import {makeStyles} from '@material-ui/core/styles';
import {amber, green} from "@material-ui/core/colors";
import FormGroup from "@material-ui/core/FormGroup";
import clsx from "clsx";
import Grid from "@material-ui/core/Grid";
import Layout from "../layout/Layout";
import Typography from "@material-ui/core/Typography";
import Breadcrumbs from "@material-ui/core/Breadcrumbs";
import Link from "@material-ui/core/Link";
import Paper from "@material-ui/core/Paper";
import {CKEditor} from '@ckeditor/ckeditor5-react';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import CustomFileInput from "reactstrap/es/CustomFileInput";
import FormControlLabel from "@material-ui/core/FormControlLabel";
import Checkbox from "@material-ui/core/Checkbox";
import {UserContext} from "../context/UserContext";
import {sendMessageAPI} from '../api/message';
import {lorexFileUpload} from "../api/enitity.crud";
import CircularProgress from "@material-ui/core/CircularProgress";

const useStyles = makeStyles(theme => ({
    form: {
        // width: '100%'
    },

    dense: {
        marginTop: 16,
        fontSize: 14
    },

    textFieldtwofield: {
        // marginLeft: theme.spacing(1),
        // marginRight: theme.spacing(1),
        width: '100%',
    },

    container: {
        // display: 'flex',
        // flexWrap: 'wrap',
        // width: '100%'
        // transform: 'scale(0.9)'
    },
    baseColor: {
        marginTop: 20,
        textAlign: 'center',
        color: '#4D5D98'
    },

    fileUpapiLoading: {
        zIndex: 0,
        marginTop: 22,
    },

    submitButton: {
        marginTop: 15,
        float: 'right',
        display: 'inline-flex'
    },

    loader: {
        marginTop: 7,
    },
    success: {
        backgroundColor: green[600],
    },

    fileError: {
        color: '#fd397a'
    },

    restButton: {

        marginLeft: 20,
    },

}));
const useStylesFacebook = makeStyles({
    root: {
        position: 'relative',
    },
    top: {
        color: '#eef3fd',
    },
    bottom: {
        color: '#6798e5',
        animationDuration: '550ms',
        position: 'absolute',
        left: 0,
    },
});

function FacebookProgress(props) {
    const classes = useStylesFacebook();

    return (
        <div className={classes.root}>
            <CircularProgress

                variant="determinate"
                value={100}
                className={classes.top}
                size={24}
                thickness={4}
                {...props}
            />
            <CircularProgress
                variant="indeterminate"
                disableShrink
                className={classes.bottom}
                size={24}
                thickness={4}
                {...props}
            />
        </div>
    );
}

const AdminSendMessageForm = (props) => {
    const classes = useStyles();
    const entity_id = props.match.params.id;
    const [content, setContent] = useState({value: '', error: ' '});
    const [subject, setSubject] = useState({value: '', error: ' '});
    const [sendasEmail, setSendasEmail] = useState(false);

    const [fileLink, setFileLink] = useState({value: '', error: ' ', success: ' '});
    const [fileSize, setFileSize] = useState({value: '', error: ' '});
    const [fileName, setFileName] = useState({value: '', error: ' '});
    const [apiLoading, setApiLoading] = useState(false);
    const sendMessageSubmission = async (event) => {
        event.preventDefault();
        let formData = new FormData();
        formData.append('eid', entity_id);
        formData.append('subject', subject.value);
        formData.append('message', content.value)
        const response = await sendMessageAPI(formData);
        console.log(response);
    }


    const fileChange = async (e) => {
        if (e.target.files[0]) {
            setApiLoading(true);
            setFileLink({...fileLink, value: '', success: ' '});
            setFileSize({...fileSize, value: ''});
            setFileName({...fileName, value: ''});
            let formData = new FormData();
            formData.append('file', e.target.files[0]);
            const filename = e.target.files[0].name;
            const response = await lorexFileUpload(formData);
            if (response.error === false) {
                setFileLink({...fileLink, value: response.record_id, success: 'uploaded'});
                setFileSize({...fileSize, value: response.file_size});

                if (filename) {
                    setFileName({...fileName, value: filename});
                    setApiLoading(false);
                }
            } else {
                setFileLink({...fileLink, error: response.message.file[0]});
                setApiLoading(false);
            }
        } else {
            setFileLink({...fileLink, value: '', success: ' '});
            setFileSize({...fileSize, value: ''});
            setFileName({...fileName, value: ''});
        }
    }


    return (
        <>
            <div className="container">
                <div className="message-title">
                    <Typography className={classes.baseColor} color="inherit" variant="h5">Send Message</Typography>
                </div>
                <form onSubmit={sendMessageSubmission} className={classes.container} noValidate autoComplete="off">
                    <FormGroup row>

                        <div className={'col-md-12'}>
                            <FormControlLabel
                                control={<Checkbox color="primary"/>}
                                label="Send as mail"
                                className={'send-as-mail'}
                                labelPlacement="start"
                                onChange={e => setSendasEmail(
                                    e.target.checked
                                )}
                            />
                        </div>

                        {/*{sendasEmail === true ?*/}
                        {/*<div className={'col-md-12'}>*/}
                        {/*    <TextField className={clsx(classes.textFieldtwofield, classes.dense)}*/}
                        {/*               id="standard-basic"*/}
                        {/*               label="To"/>*/}
                        {/*</div> : ''*/}
                        {/*}*/}

                        <div className={'col-md-12 custom-subject'}>
                            <TextField disabled={apiLoading} value={subject.value} onChange={(e) => setSubject({...subject, value: e.target.value})} className={clsx(classes.textFieldtwofield, classes.dense)} id="standard-basic"
                                       label="Subject"/>
                        </div>
                        <div className={'col-md-12'}>

                            <CKEditor
                                disable={apiLoading}
                                editor={ClassicEditor}
                                data={content.value}
                                onReady={editor => {
                                    // You can store the "editor" and use when it is needed.
                                    // console.log('Editor is ready to use!', editor);
                                }}
                                onChange={(event, editor) => {
                                    const data = editor.getData();
                                    // console.log({event, editor, data});
                                    setContent({...content, value: data})
                                }}
                                // onBlur={(event, editor) => {
                                //     console.log('Blur.', editor);
                                // }}
                                // onFocus={(event, editor) => {
                                //     console.log('Focus.', editor);
                                // }}
                            />

                        </div>

                        <div className={'col-md-12'}>
                            <CustomFileInput
                                disable={apiLoading}
                                value={fileLink.value.File}
                                onChange={e => fileChange(e)}
                                required
                                id="attachment"
                                label="File"
                                className={clsx(classes.fileUploading, classes.dense)}
                                margin="dense"
                            />
                            {fileLink.success !== ' ' ? (<span>{fileLink.success}</span>) : ' '}
                            {fileLink.error !== ' ' ? (
                                <span className={clsx(classes.fileError)}>{fileLink.error}</span>) : ' '}
                        </div>
                        <div className={'col-md-12'}>
                            <div className={clsx(classes.submitButton, 'custom-button-wrapper')}>
                                {apiLoading ? (
                                        <div className={clsx(classes.loader)}>
                                            <FacebookProgress/>
                                        </div>)
                                    : null}
                                <input disabled={apiLoading}
                                       className={clsx('btn btn-primary', classes.restButton)}
                                       type="submit" value="Send Message"/>
                            </div>
                        </div>
                    </FormGroup>
                </form>
            </div>

        </>
    )
}

export default AdminSendMessageForm;
