import React, {useContext, useEffect, useState} from 'react';
import TextField from '@material-ui/core/TextField';
import {makeStyles} from '@material-ui/core/styles';
import {green} from "@material-ui/core/colors";
import FormGroup from "@material-ui/core/FormGroup";
import clsx from "clsx";
import Grid from "@material-ui/core/Grid";
import Typography from "@material-ui/core/Typography";
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
import CKEditor from 'ckeditor4-react';
import Button from '@material-ui/core/Button';
import IconButton from '@material-ui/core/IconButton';
import CloseIcon from '@material-ui/icons/Close';
import {DropzoneArea, DropzoneDialogBase} from "material-ui-dropzone";
import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';
import ListItemAvatar from '@material-ui/core/ListItemAvatar';
import ListItemText from '@material-ui/core/ListItemText';
import Avatar from '@material-ui/core/Avatar';
import PictureAsPdfIcon from '@material-ui/icons/PictureAsPdf';
import ListItemSecondaryAction from '@material-ui/core/ListItemSecondaryAction';
import DeleteIcon from '@material-ui/icons/Delete';

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
        marginBottom: 20,
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
    },

    demo: {
        backgroundColor: '#F0F0F6',
    },

    attachmentSection: {
        marginTop: 10,
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
    const {loading, addError, errorList, role, attributes, manageOuterThreads} = useContext(UserContext);

    const entity_id = props.match.params.id ? props.match.params.id : attributes.organization;
    const [content, setContent] = useState({value: '', error: ' '});
    const [subject, setSubject] = useState({value: '', error: ' '});
    const [sendasEmail, setSendasEmail] = useState(false);
    const [messageType, setMessageType] = useState({value: '', error: ' '});
    const [fileLink, setFileLink] = useState({value: '', error: ' ', success: ' '});
    const [fileSize, setFileSize] = useState({value: '', error: ' '});
    const [fileName, setFileName] = useState({value: '', error: ' '});
    const [apiLoading, setApiLoading] = useState(false);
    const [templates, setTemplates] = useState([]);
    const [noteType, setNoteType] = useState({email: 'Email', note: 'Note', phone: 'Phone', mail: 'Mail'});
    const [noteChosen, setNoteChosen] = useState({value: '', error: ' '});

    const [open, setOpen] = React.useState(false);
    const [fileObjects, setFileObjects] = React.useState([]);
    const [secondary, setSecondary] = React.useState(false);
    const [fileTest, setFileTest] = React.useState('');

    useEffect(() => {
        if (loading === true) {
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

    const dialogTitle = () => (
        <>
            <span>Upload file</span>
            <IconButton
                style={{right: '12px', top: '8px', position: 'absolute'}}
                onClick={() => setOpen(false)}>
                <CloseIcon/>
            </IconButton>
        </>
    );


    const removeFileFromArray = (index) => {
        let array = [...fileObjects];
        if (index > -1) {
            array.splice(index, 1);
        }
        setFileObjects(array);
    }

    const sendMessageSubmission = async (event) => {
        event.preventDefault();


        setApiLoading(true);
        resetFormError();
        const noteValue = sendasEmail ? 1 : 0;
        let formData = new FormData();
        formData.append('eid', entity_id);
        formData.append('subject', subject.value);
        if (sendasEmail) {
            formData.append('note', '1');
        } else {
            formData.append('note', '0');
        }
        if (noteChosen.value) {
            formData.append('notetype', noteChosen.value);
        }
        formData.append('message', content.value);

        // fileObjects?.map((anObjectMapped, index))
        // {
        //     formData.append('attachment[]', anObjectMapped)
        // }


        fileObjects.map(function(val, index){
            formData.append('attachment[]', val.file)
        });

        if (sendasEmail === true) {
            if (noteChosen.value === '') {
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
                manageOuterThreads(true);
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

    function generate(element) {
        return [0, 1, 2].map((value) =>
            React.cloneElement(element, {
                key: value,
            }),
        );
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
        if (newValue) {
            await getTemplate(newValue.id).then(response => {
                if (response.status === true) {
                    setContent({...content, value: response.data.message});
                    setSubject({...subject, value: newValue.subject});
                    setApiLoading(false);
                }
            })
        } else {
            setApiLoading(false);
        }
    }


    const resetForm = () => {
        setContent({...content, value: ''})
        setSubject({...subject, value: ''})
        setFileObjects([]);

    }


    const resetFormError = () => {
        setContent({...content, error: ' '})
        setSubject({...subject, error: ' '})
        setNoteChosen({...noteChosen, error: ' '})

    }

    const bytesToSize = (bytes) => {
        let sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        if (bytes == 0) return '0 Byte';
        let i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
        return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
    }

    return (
        <>

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
                                    {
                                        Object.entries(noteType).map(([key, val]) =>
                                            <option key={key} value={key}>{val}</option>
                                        )
                                    }


                                </Select>
                                <FormHelperText>{noteChosen.error}</FormHelperText>
                            </FormControl>
                        </div> : ''}

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
                                data={content.value}
                                onChange={(event) => {
                                    const data = event.editor.getData();
                                    setContent({...content, value: data})
                                }}


                            />
                            <FormHelperText>{content.error}</FormHelperText>
                        </FormControl>

                    </div>

                    <div className={'col-md-12'}>

                        <Button variant="contained" color="primary" onClick={() => setOpen(true)}>
                            Attach Files
                        </Button>

                        {fileLink.success !== ' ' ? (<span>{fileLink.success}</span>) : ' '}
                        {fileLink.error !== ' ' ? (
                            <span className={clsx(classes.fileError)}>{fileLink.error}</span>) : ' '}
                        {fileObjects.length !== 0 ?
                            <Grid className={classes.attachmentSection} container spacing={2}>
                                <Grid item xs={12} md={12}>
                                    <Typography variant="h6" className={classes.title}>
                                        Attached Files
                                    </Typography>
                                    <div className={classes.demo}>
                                        <List>
                                            {fileObjects?.map((anObjectMapped, index) =>


                                                <ListItem key={index}>
                                                    <ListItemAvatar>
                                                        <Avatar>
                                                            <PictureAsPdfIcon/>
                                                        </Avatar>
                                                    </ListItemAvatar>
                                                    <ListItemText
                                                        primary={anObjectMapped.file.name}
                                                        secondary={bytesToSize(anObjectMapped.file.size)}
                                                    />
                                                    <ListItemSecondaryAction>
                                                        <IconButton edge="end" aria-label="delete">
                                                            <DeleteIcon onClick={(e) => removeFileFromArray(index)}/>
                                                        </IconButton>
                                                    </ListItemSecondaryAction>
                                                </ListItem>
                                            )}


                                        </List>
                                    </div>
                                </Grid>
                            </Grid> : ''}
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
            <DropzoneDialogBase
                dialogTitle={dialogTitle()}
                acceptedFiles={['.pdf']}
                filesLimit={10}
                fileObjects={fileObjects}
                cancelButtonText={"cancel"}
                submitButtonText={"Attach Files"}
                maxFileSize={5000000}
                open={open}
                onAdd={newFileObjs => {
                    setFileObjects([].concat(fileObjects, newFileObjs));
                }}

                onClose={() => setOpen(false)}
                onSave={() => {
                    setOpen(false);
                }}
                useChipsForPreview
                fullWidth={true}
                maxWidth={'md'}
            />
        </>
    )
}

export default AdminSendMessageForm;
