import React, {useContext, useEffect} from 'react';
import Layout from "../layout/Layout";
import MaterialTable from "material-table";
import {UserContext} from "../context/UserContext";
import Grid from "@material-ui/core/Grid";
import FormGroup from "@material-ui/core/FormGroup";
import TextField from "@material-ui/core/TextField";
import clsx from "clsx";
import FormControlLabel from "@material-ui/core/FormControlLabel";
import Checkbox from "@material-ui/core/Checkbox";
import FormControl from "@material-ui/core/FormControl";
import InputLabel from "@material-ui/core/InputLabel";
import Select from "@material-ui/core/Select";
import FormHelperText from "@material-ui/core/FormHelperText";
import Paper from "@material-ui/core/Paper";
import {makeStyles} from "@material-ui/core/styles";
import {amber, green} from "@material-ui/core/colors";
import {EntityList} from "../api/enitity.crud";
import Autocomplete from "@material-ui/lab/Autocomplete";
import {FetchMessageLogs, FetchThreads} from "../api/message";
import VisibilityIcon from "@material-ui/icons/Visibility";
import Modal from "react-modal";
import NewChatPanelForLogs from "./NewChatPanelForLogs";

const useStyles = makeStyles(theme => ({
    root: {
        flexGrow: 1,
    },
    backButton: {
        marginRight: theme.spacing(1),
    },
    instructions: {
        marginTop: theme.spacing(1),
        marginBottom: theme.spacing(1),
    },
    adjustment: {
        marginRight: '5px',
    },
    companyinfo: {
        listStyle: 'none',
        padding: '0px',
        minHeight: '100px'

    },
    listItem: {
        marginBottom: '5px'
    },
    textField: {
        // marginLeft: theme.spacing(1),
        // marginRight: theme.spacing(1),
        width: '100%',
        marginTop: 3,
    },

    textFieldOther: {
        width: '100%',
    },

    textFieldCity: {

        marginTop: 3,
    },


    textFieldtwofield: {
        // marginLeft: theme.spacing(1),
        // marginRight: theme.spacing(1),
        width: '100%',
    },


    selectField: {
        // marginLeft: theme.spacing(1),
        // marginRight: theme.spacing(1),
        width: '100%',
        marginTop: 16,
    },

    label: {
        fontSize: 14
    },

    dense: {
        marginTop: 16,
        fontSize: 14
    },
    container: {
        display: 'flex',
        flexWrap: 'wrap',
    },
    checkbox: {
        marginTop: 30
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

    restButton: {

        marginLeft: 20,
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


    error: {
        backgroundColor: theme.palette.error.dark,
    },
    info: {
        backgroundColor: theme.palette.primary.main,
    },
    warning: {
        backgroundColor: amber[700],
    },
    icon: {
        fontSize: 20,
    },
    iconVariant: {
        opacity: 0.9,
        marginRight: theme.spacing(1),
    },
    message: {
        display: 'flex',
        alignItems: 'center',
    },

    paper: {
        paddingTop: 30,
        paddingBottom: 30,
        paddingLeft: 10,
        paddingRight: 10,
        width: '100%',
        marginBottom: 20
    },

    searchButton: {
        marginTop: 26
    }


}));


const MessageLog = (props) => {


    const {loading, attributes, addError, errorList, role, addTitle, profile, setMessageForLogs} = useContext(UserContext);
    const classes = useStyles();

    const [entityId, setEntityId] = React.useState({value: '', error: ' '});
    const [apiLoading, setApiLoading] = React.useState(false);
    const [date, setDate] = React.useState({value: '', error: ' '});
    const [date2, setDate2] = React.useState({value: '', error: ' '});
    const [entityList, setEntityList] = React.useState([]);
    const [messagesLogs, setMessagesLogs] = React.useState([]);
    const [threads, setThreads] = React.useState([]);

    const [modalIsOpen, setIsOpen] = React.useState(false);
    const [state, setState] = React.useState(false);


    useEffect(() => {
        if (loading === true) {
            addTitle('Message Logs');
            entitylisitingLoader();
        }
    }, [loading])

    function openModal() {
        setIsOpen(true);
    }

    function afterOpenModal() {
        // references are now sync'd and can be accessed.
        // subtitle.style.color = '#f00';
    }

    function closeModal() {
        setIsOpen(false);
    }

    const entitylisitingLoader = async () => {
        setApiLoading(true);
        var EntityListLocal = await JSON.parse(localStorage.getItem("EntityList") !== 'undefined' ? localStorage.getItem("EntityList") : '');
        if (EntityListLocal !== 'undefined' && EntityListLocal) {
            setEntityList(EntityListLocal);
            setApiLoading(false);
        } else {
            const response = await EntityList();

            if (response.error) {
                addError(response.error.message);
            }

            if (response.type === 'error') {
                window.location.reload();
            }

            if (response.status === 401) {
                window.location.reload();
            }


            localStorage.setItem("EntityList", JSON.stringify(response.data));
            await setEntityList(response.data);
            setApiLoading(false);
        }
    }

    const handleOnSubmit = async (event) => {
        event.preventDefault();
        setApiLoading(true);
        let formData = new FormData();
        formData.append('eid', entityId.value);
        formData.append('startDate', date.value);
        formData.append('endDate', date2.value);
        const response = await FetchMessageLogs(formData);
        if (response.status === true) {
            setMessagesLogs(response.data);
            setApiLoading(false);
        }

        if (response.status === true) {
            if(response.data.length !== 0){
                FetchThreads(entityId.value).then(response => {
                    setMessageForLogs(response.data)
                })
            }
        }

    }


    return (
        <Layout>

            <Grid container spacing={0}>
                <Paper className={classes.paper} elevation={3}>
                    <form onSubmit={handleOnSubmit} noValidate
                          autoComplete="off">
                        <FormGroup row>
                            <div className={'col-md-4'}>
                                <FormControl className={clsx(classes.selectField)}
                                             error={entityId.error !== ' '}>
                                    <Autocomplete
                                        onChange={(event, newValue) => {
                                            if (newValue) {
                                                setEntityId({
                                                    ...entityId,
                                                    value: newValue.id
                                                })
                                            } else {
                                                setEntityId({
                                                    ...entityId,
                                                    value: ''
                                                })
                                            }
                                        }}
                                        disabled={apiLoading}
                                        id="combo-box-demo"
                                        options={entityList ? entityList : ''}
                                        getOptionLabel={(option) => option.account_name}
                                        renderInput={(params) => <TextField error={entityId.error !== ' '} {...params}
                                                                            label="Entity Name" variant="outlined"/>}
                                    />
                                    <FormHelperText>{entityId.error}</FormHelperText>
                                </FormControl>
                            </div>


                            <div className={'col-md-3'}>
                                <TextField
                                    required
                                    label="Start Date"
                                    format={'Y-M-d'}
                                    inputProps={{
                                        name: 'inputFormationDate',
                                        id: 'inputFormationDate',
                                    }}
                                    onChange={e => setDate({
                                        ...date,
                                        value: e.target.value
                                    })}
                                    InputLabelProps={{
                                        shrink: true,
                                    }}
                                    type="date"
                                    className={clsx(classes.textFieldOther, classes.dense)}
                                />
                            </div>

                            <div className={'col-md-3'}>
                                <TextField
                                    required
                                    label="End Date"
                                    format={'Y-M-d'}
                                    inputProps={{
                                        name: 'inputFormationDate',
                                        id: 'inputFormationDate',
                                    }}
                                    onChange={e => setDate2({
                                        ...date2,
                                        value: e.target.value
                                    })}
                                    InputLabelProps={{
                                        shrink: true,
                                    }}
                                    type="date"
                                    className={clsx(classes.textFieldOther, classes.dense)}
                                />
                            </div>

                            <div className={'col-md-2'}>
                                <div
                                    className={clsx(classes.submitButton, 'custom-button-wrapper', classes.searchButton)}>
                                    <input
                                        className={clsx('btn btn-primary', classes.restButton)}
                                        type="submit" value="Search"/>
                                </div>
                            </div>
                        </FormGroup>
                    </form>
                </Paper>
            </Grid>


            <div style={{maxWidth: "100%"}}>
                <MaterialTable
                    isLoading={apiLoading}
                    columns={[
                        {title: "Entity", field: "name"},
                        {title: "Subject", field: "subject"},
                        {title: "Date", field: "sendTime"},

                    ]}
                    data={messagesLogs}
                    actions={[
                        rowData => ({
                            icon: () => <VisibilityIcon/>,
                            tooltip: 'View',
                            onClick: (event, rowData) => {
                                if (rowData.id) {
                                    if(rowData.groupId !== "0") {
                                        localStorage.setItem('activeLogThread', rowData.groupId);
                                    } else {
                                        localStorage.setItem('activeLogThread', rowData.id);
                                    }
                                    localStorage.setItem('ThreadBelongsTo', rowData.name);
                                    localStorage.setItem('activeLogMessage', rowData.id);
                                    openModal();
                                }
                            }
                        })
                    ]}
                    title="Messages"
                />
            </div>


            <div>
                <Modal
                    parentSelector={() => document.querySelector('#messageModal')}
                    isOpen={modalIsOpen}
                    onAfterOpen={afterOpenModal}
                    onRequestClose={closeModal}
                    contentLabel="Chat Application"
                    style={{
                        overlay: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)'
                        },
                    }}
                >
                    <div className="chat-wrapper">
                        <NewChatPanelForLogs/>
                    </div>
                </Modal>
            </div>

        </Layout>
    )
}


export default MessageLog;
