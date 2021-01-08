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
        paddingLeft:10,
        paddingRight:10,
        width:'100%',
        marginBottom:20
    },

    searchButton: {
        marginTop: 26
    }


}));



const MessageLog = (props) => {


    const {loading, attributes, addError, errorList, role, addTitle, profile} = useContext(UserContext);
    const classes = useStyles();

    const [entityId, setEntityId] = React.useState({value: '', error: ' '});
    const [apiLoading, setApiLoading] = React.useState(false);
    const [entityList, setEntityList] = React.useState([]);


    useEffect(() => {
        if (loading === true) {
            addTitle('Message Logs');
            entitylisitingLoader();
        }
    }, [loading])

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

    const handleOnSubmit = () => {

    }



    return(
        <Layout>

            <Grid container spacing={0}>
                <Paper className={classes.paper} elevation={3} >
                <form onSubmit={handleOnSubmit} noValidate
                      autoComplete="off">
                    <FormGroup row>
                        <div className={'col-md-5'}>
                            <FormControl className={clsx(classes.selectField)}
                                         error={entityId.error !== ' '}>
                                <Autocomplete
                                    onChange={(event, newValue) => {
                                        if(newValue) {
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

                                    id="combo-box-demo"
                                    options={entityList ? entityList : ''}
                                    getOptionLabel={(option) => option.account_name}
                                    renderInput={(params) => <TextField error={entityId.error !== ' '} {...params} label="Entity Name" variant="outlined" />}
                                />
                                <FormHelperText>{entityId.error}</FormHelperText>
                            </FormControl>
                        </div>


                        <div className={'col-md-5'}>
                            <TextField
                                required
                                label="Date"
                                format={'Y-M-d'}
                                inputProps={{
                                    name: 'inputFormationDate',
                                    id: 'inputFormationDate',
                                }}
                                InputLabelProps={{
                                    shrink: true,
                                }}
                                type="date"
                                className={clsx(classes.textFieldOther, classes.dense)}
                            />
                        </div>

                        <div className={'col-md-2'}>
                            <div className={clsx(classes.submitButton, 'custom-button-wrapper', classes.searchButton)}>
                                <input
                                       className={clsx('btn btn-primary', classes.restButton)}
                                       type="submit" value="Search"/>
                            </div>
                        </div>
                    </FormGroup>
                </form>
                </Paper>
            </Grid>





            <div style={{ maxWidth: "100%" }}>
                <MaterialTable
                    columns={[
                        { title: "Entity", field: "name" },
                        { title: "Subject", field: "surname" },
                        { title: "Date", field: "birthYear", type: "numeric" },

                    ]}
                    data={[
                        {
                            name: "Mehmet",
                            surname: "Baran",
                            birthYear: '2020-12-14 13:37:17',
                        },
                    ]}
                    title="Messages"
                />
            </div>
        </Layout>
    )
}


export default MessageLog;
