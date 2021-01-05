import React, {useContext, useEffect, useState} from 'react';
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
import FormControl from "@material-ui/core/FormControl";
import InputLabel from "@material-ui/core/InputLabel";
import Select from "@material-ui/core/Select";
import FormHelperText from "@material-ui/core/FormHelperText";
import {TemplateList, getTemplate} from "../api/message";
import Autocomplete from "@material-ui/lab/Autocomplete";
import {toast} from 'react-toastify';

const useStyles = makeStyles(theme => ({
    form: {
        // width: '100%'
    },

    dense: {
        marginTop: 16,
        fontSize: 14
    },

    subjectDense: {
        marginTop: 0,
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

    selectField: {
        width: '100%'
    }

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
    const {loading, addError, errorList, role} = useContext(UserContext);

    const entity_id = props.match.params.id;
    const [content, setContent] = useState({value: '', error: ' '});
    const [subject, setSubject] = useState({value: '', error: ' '});
    const [sendasEmail, setSendasEmail] = useState(false);
    const [messageType, setMessageType] = useState({value: '', error: ' '});
    const [fileLink, setFileLink] = useState({value: '', error: ' ', success: ' '});
    const [fileSize, setFileSize] = useState({value: '', error: ' '});
    const [fileName, setFileName] = useState({value: '', error: ' '});
    const [apiLoading, setApiLoading] = useState(false);
    const [templates, setTemplates] = useState([]);
    const [noteType, setNoteType] = useState({email: 'Email', note: 'Note', phone: 'Phone', mail:'Mail'});
    const [noteChosen, setNoteChosen] = useState({value: '', error: ' '});
    useEffect(() => {
        if(loading === true) {
            getTemplateList();
        }
    }, [loading]);

    const getTemplateList = async () => {
        try {
            await TemplateList().then(response => {
                setTemplates(response.data.results);
            })
        } catch (e) {
            addError(e)
        }
    }

    const sendMessageSubmission = async (event) => {
        event.preventDefault();
        setApiLoading(true);
        resetFormError();
        const noteValue = sendasEmail ? 1 : 0;
        let formData = new FormData();
        formData.append('eid', entity_id);
        formData.append('subject', subject.value);
        if(sendasEmail) {
            formData.append('note', '1');
        } else {
            formData.append('note', '0');
        }
        if(noteChosen.value) {
            formData.append('notetype', noteChosen.value);
        }
        formData.append('message', content.value);

        if(sendasEmail === true){
            if(noteChosen.value  === ''){
                setNoteChosen({...noteChosen, error: 'Choose the note type'});
                toast.error('Choose the note type', {
                    position: toast.POSITION.BOTTOM_LEFT
                });
                setApiLoading(false);
                return false;
            }
        }
        await sendMessageAPI(formData).then(response => {
            if (response.status === true) {
                setApiLoading(false);
                toast.success(response.message, {
                    position: toast.POSITION.BOTTOM_LEFT
                });
                resetForm()
            }

            if (response.status === false) {
                setApiLoading(false);
                if (response.error) {


                    Object.keys(response.error).forEach((key, index) => {
                        if (key === 'subject') {
                            setSubject({...subject, error: response.error[key]})
                            toast.error(response.error[key], {
                                position: toast.POSITION.BOTTOM_LEFT
                            });
                        }

                        if (key === 'message') {
                            setContent({...subject, error: response.error[key]})
                            toast.error(response.error[key], {
                                position: toast.POSITION.BOTTOM_LEFT
                            });
                        }
                    })
                }
            }

        });

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

    const handleTemplateListing = async (newValue) => {

        setApiLoading(true);
        if (newValue) {
            setMessageType({
                ...messageType,
                value: newValue.id
            })
        } else {
            setMessageType({
                ...messageType,
                value: ''
            })
        }
        if (newValue.id) {
            await getTemplate(newValue.id).then(response => {
                if (response.status === true) {
                    setContent({...content, value: response.data.message});
                    setSubject({...subject, value:newValue.subject});
                    setApiLoading(false);
                }
            })
        } else {
            setApiLoading(false);
        }
    }


    const resetForm = () => {
        setContent({...content, value: ''})
        setSubject({...subject, value:''})

    }



    const resetFormError = () => {
        setContent({...content, error: ' '})
        setSubject({...subject, error:' '})
        setNoteChosen({...noteChosen, error:' '})

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
                                label="Send as Note"
                                className={'send-as-mail'}
                                labelPlacement="start"
                                onChange={e => setSendasEmail(
                                    e.target.checked
                                )}
                            />
                        </div>

                        {/*<div className={'col-md-12'}>*/}
                        {/*    <FormControl className={clsx(classes.selectField)}*/}
                        {/*                 error={messageType.error !== ' '}>*/}
                        {/*        <InputLabel className={clsx(classes.label)} htmlFor="age-native-simple">Message*/}
                        {/*            Type</InputLabel>*/}
                        {/*        <Select*/}
                        {/*            disabled={apiLoading}*/}
                        {/*            required*/}
                        {/*            error={messageType.error !== ' '}*/}
                        {/*            value={messageType.value}*/}

                        {/*            onChange={e => handleTemplateListing(e)}*/}
                        {/*            inputProps={{*/}
                        {/*                name: 'messageType',*/}
                        {/*                id: 'messageType',*/}
                        {/*            }}>*/}
                        {/*            <option value=""/>*/}
                        {/*            {templates?.map((anObjectMapped, index) => <option key={index}*/}
                        {/*                                                               value={anObjectMapped.id}>{anObjectMapped.subject}</option>)*/}

                        {/*            }*/}

                        {/*        </Select>*/}
                        {/*        <FormHelperText>{messageType.error}</FormHelperText>*/}
                        {/*    </FormControl>*/}
                        {/*</div>*/}

                        <div className={'col-md-12'}>
                            <FormControl className={clsx(classes.selectField)}
                                         error={messageType.error !== ' '}>
                                <Autocomplete
                                    disabled={apiLoading}
                                    onChange={(event, newValue) => {

                                        handleTemplateListing(newValue)
                                    }}

                                    id="combo-box-demo"
                                    options={templates ? templates : ''}
                                    getOptionLabel={(option) => option.subject}
                                    renderInput={(params) => <TextField error={messageType.error !== ' '} {...params}
                                                                        label="Message Type" variant="outlined"/>}
                                />
                                <FormHelperText>{messageType.error}</FormHelperText>
                            </FormControl>
                        </div>
                        {sendasEmail ?
                            <div className={'col-md-12'}>
                                <FormControl className={clsx(classes.selectField)}
                                             error={noteChosen.error !== ' '}>
                                    <InputLabel className={clsx(classes.label)} htmlFor="age-native-simple">Note
                                        Type</InputLabel>
                                    <Select
                                        disabled={apiLoading}
                                        required
                                        error={noteChosen.error !== ' '}
                                        value={noteChosen.value}

                                        onChange={e => setNoteChosen({
                                            ...noteChosen,
                                            value: e.target.value
                                        })}
                                        inputProps={{
                                            name: 'noteChosen',
                                            id: 'noteChosen',
                                        }}>
                                        <option value=""/>
                                        {/*{noteType?.map((anObjectMapped, index) => <option key={index} value={anObjectMapped}>{index}</option>)}*/}

                                        {/*{noteType.map((value, index) => (*/}
                                        {/*    <option key={index} value={index}>{value}</option>*/}
                                        {/*))}*/}
                                        {
                                            Object.entries(noteType).map(([key, val]) =>
                                                <option key={key} value={key}>{val}</option>
                                            )
                                        }


                                    </Select>
                                    <FormHelperText>{noteChosen.error}</FormHelperText>
                                </FormControl>
                            </div> : ''}

                        {/*{sendasEmail === true ?*/}
                        {/*<div className={'col-md-12'}>*/}
                        {/*    <TextField className={clsx(classes.textFieldtwofield, classes.dense)}*/}
                        {/*               id="standard-basic"*/}
                        {/*               label="To"/>*/}
                        {/*</div> : ''*/}
                        {/*}*/}

                        <div className={'col-md-12 custom-subject'}>
                            <TextField disabled={apiLoading} value={subject.value}
                                       error={subject.error !== ' '}
                                       helperText={subject.error}
                                       onChange={(e) => setSubject({...subject, value: e.target.value})}
                                       className={clsx(classes.textFieldtwofield, classes.subjectDense)} id="standard-basic"
                                       label="Subject"/>
                        </div>
                        <div className={'col-md-12'}>
                            <FormControl className={clsx(classes.selectField)}
                                         error={content.error !== ' '}>
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
                                <FormHelperText>{content.error}</FormHelperText>
                            </FormControl>

                        </div>

                        {/*<div className={'col-md-12'}>*/}
                        {/*    <CustomFileInput*/}
                        {/*        disable={apiLoading}*/}
                        {/*        value={fileLink.value.File}*/}
                        {/*        onChange={e => fileChange(e)}*/}
                        {/*        required*/}
                        {/*        id="attachment"*/}
                        {/*        label="File"*/}
                        {/*        className={clsx(classes.fileUploading, classes.dense)}*/}
                        {/*        margin="dense"*/}
                        {/*    />*/}
                        {/*    {fileLink.success !== ' ' ? (<span>{fileLink.success}</span>) : ' '}*/}
                        {/*    {fileLink.error !== ' ' ? (*/}
                        {/*        <span className={clsx(classes.fileError)}>{fileLink.error}</span>) : ' '}*/}
                        {/*</div>*/}
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
