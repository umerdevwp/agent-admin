import React, {useState} from 'react';
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

const useStyles = makeStyles(theme => ({
    form: {
        width: '100%'
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

        transform: 'scale(0.9)'
    },
    baseColor: {
        marginTop: 20,
        textAlign: 'center',
        color: '#4D5D98'
    },

}));
const SendMessageForm = () => {
    const classes = useStyles();
    const [content, setContent] = useState('');
    const onChange = (evt) => {
        var newContent = evt.editor.getData();
        setContent(newContent)
    }

    return (
        <>
            <div className="container">
                <div className="message-title">
                    <Typography className={classes.baseColor} color="inherit" variant="h5">Send Message</Typography>
                </div>
                <form className={classes.container} noValidate autoComplete="off">
                    <FormGroup row>

                        {/*<div className={'col-md-12'}>*/}
                        {/*    <TextField className={clsx(classes.textFieldtwofield, classes.dense)} id="standard-basic"*/}
                        {/*               label="To"/>*/}
                        {/*</div>*/}
                        {/*<div className={'col-md-12'}>*/}
                        {/*    <TextField className={clsx(classes.textFieldtwofield, classes.dense)} id="standard-basic"*/}
                        {/*               label="From"/>*/}
                        {/*</div>*/}
                        {/*<div className={'col-md-12'}>*/}
                        {/*    <TextField className={clsx(classes.textFieldtwofield, classes.dense)} id="standard-basic"*/}
                        {/*               label="Subject"/>*/}
                        {/*</div>*/}
                        <div className={'col-md-12'}>

                            <CKEditor
                                editor={ClassicEditor}
                                data="<p>Hello from CKEditor 5!</p>"
                                onReady={editor => {
                                    // You can store the "editor" and use when it is needed.
                                    console.log('Editor is ready to use!', editor);
                                }}
                                onChange={(event, editor) => {
                                    const data = editor.getData();
                                    console.log({event, editor, data});
                                }}
                                onBlur={(event, editor) => {
                                    console.log('Blur.', editor);
                                }}
                                onFocus={(event, editor) => {
                                    console.log('Focus.', editor);
                                }}
                            />

                        </div>
                        {/*<div className={'col-md-12'}>*/}
                        {/*    <TextField className={clsx(classes.textFieldtwofield, classes.dense)} id="standard-basic"*/}
                        {/*               label="File Title"/>*/}
                        {/*</div>*/}
                        <div className={'col-md-12'}>
                            <CustomFileInput
                                required
                                id="attachment"
                                label="File"
                                className={clsx(classes.fileUploading, classes.dense)}
                                margin="dense"
                            />
                        </div>
                        {/*<div className={'col-md-12'}>*/}
                        {/*    <FormControlLabel*/}
                        {/*        control={<Checkbox color="primary"/>}*/}
                        {/*        label="Send as mail"*/}
                        {/*        className={'send-as-mail'}*/}
                        {/*        labelPlacement="start"*/}
                        {/*    />*/}
                        {/*</div>*/}

                        <div className={'col-md-12'}>
                            <div className={clsx(classes.submitButton, 'custom-button-message')}>
                                <input
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

export default SendMessageForm;
