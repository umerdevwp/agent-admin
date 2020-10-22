import React, {useEffect, useContext, forwardRef } from 'react';
import MaterialTable, {MTableToolbar} from "material-table";
import Layout from "../layout/Layout";
import {entityList} from "../api/enitity.crud";
import {UserContext} from '../context/UserContext';
import {withOktaAuth} from '@okta/okta-react';
import {useHistory} from "react-router-dom";
import VisibilityIcon from '@material-ui/icons/Visibility';
import Link from '@material-ui/core/Link';
import SnackbarContent from "@material-ui/core/SnackbarContent";
import clsx from "clsx";
import IconButton from "@material-ui/core/IconButton";
import CloseIcon from "@material-ui/icons/Close";
import PropTypes from "prop-types";
import {makeStyles} from "@material-ui/core/styles";
import {amber, green} from "@material-ui/core/colors";
import CheckCircleIcon from "@material-ui/icons/CheckCircle";
import WarningIcon from "@material-ui/icons/Warning";
import ErrorIcon from "@material-ui/icons/Error";
import InfoIcon from "@material-ui/icons/Info";
import ChildDetailedPage from '../entity/ChildDetailedPage';
import Add from '@material-ui/icons/Add';



const useStyles = makeStyles(theme => ({
    root: {
        flexGrow: 1,
    },
    paper: {
        padding: theme.spacing(2),
        textAlign: 'center',
        color: theme.palette.text.secondary,
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

    errorMessage: {
        marginBottom: '5px'
    },
    success: {
        backgroundColor: green[600],
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

}));


const variantIcon = {
    success: CheckCircleIcon,
    warning: WarningIcon,
    error: ErrorIcon,
    info: InfoIcon,
};


function MySnackbarContentWrapper(props) {
    const classes = useStyles();
    const {className, message, onClose, variant, ...other} = props;
    const Icon = variantIcon[variant];

    return (
        <SnackbarContent
            elevation={6}
            className={clsx(classes[variant], className)}
            aria-describedby="client-snackbar"
            message={
                <span id="client-snackbar" className={classes.message}>
          <Icon className={clsx(classes.icon, classes.iconVariant)}/>
                    {message}
        </span>
            }
            action={[
                <IconButton key="close" aria-label="Close" color="inherit" onClick={onClose}>
                    <CloseIcon className={classes.icon}/>
                </IconButton>,
            ]}
            {...other}
        />
    );
}

MySnackbarContentWrapper.propTypes = {
    className: PropTypes.string,
    message: PropTypes.node,
    onClose: PropTypes.func,
    variant: PropTypes.oneOf(['success', 'warning', 'error', 'info']).isRequired,
}


function Dashboard(props) {

    const classes = useStyles();
    const history = useHistory();
    const tableIcons = {
       Add: forwardRef((props, ref) => <Add {...props} ref={ref} color='action' />)

};
    const {loading, attributes, addError, errorList, role, addTitle, removeError} = useContext(UserContext);
    const checkRole = role ? role : localStorage.getItem('role');
    const [entitydata, setEntityData] = React.useState([]);
    const [componentLoading, setComponentLoading] = React.useState(true);
    useEffect(() => {
        if (loading === true) {
            addTitle('Dashboard');
            if (role === 'Parent Organization' || role === 'Administrator') {
                asyncDataFetch();
            }
        }

    }, [loading])

    const asyncDataFetch = async () => {
        const tokenResult = await props.authState.accessToken;
        ;

        try {
            await fetchData(tokenResult);
        } catch (e) {
            addError('Something when wrong!!');
        }
    }


    const fetchData = async (token) => {
        try {
            await entityList(token).then(response => {
                if (response.data) {
                    setEntityData(response.data.results);
                    setComponentLoading(false);
                }

                if (response.error) {
                    addError(response.error.message);
                }

                if (response.type === 'error') {
                    window.location.reload();
                }

                if (response.status === 401) {
                    window.location.reload();
                }


            });
        } catch (e) {
            addError(e);
        }
    }

    const settingData = {
        columns: [

            {title: 'Name', field: 'name'},
            {title: 'Entity Structure', field: 'entityStructure'},
            {title: 'Filing State', field: 'filingState'},
            {title: 'Formation Date', field: 'formationDate'},
            // {
            //     title: 'Action',
            //     sorting: false,
            //     field: 'url',
            //
            //     render: rowData =>  <Link
            //         component="button"
            //         variant="body2"
            //         onClick={() => {
            //             if (rowData.id !== attributes.organization) {
            //                 history.push(`/entity/${rowData.id}`);
            //             } else {
            //                 history.push(`/entity`);
            //             }
            //         }}>
            //         {/*<VisibilityIcon/>*/}
            //     </Link>
            //
            //     // render: rowData => <a href={`/entity/${rowData.id}`}> <VisibilityIcon/> </a>
            // },
        ],
        data: entitydata,
    };

    return (
        <>

            <Layout>

                {errorList?.map((value, index) => (
                    <MySnackbarContentWrapper className={classes.errorMessage} spacing={1} index={index} variant="error"
                                              message={value} onClose={() => {
                        removeError(index)
                    }}/>
                ))}
                {checkRole === 'Parent Organization' ?
                    <div style={{maxWidth: "100%"}}>
                        <MaterialTable
                            icons={tableIcons}
                            parentChildData={(row, rows) => rows.find(a => a.id === row.parentId)}
                            isLoading={componentLoading}
                            title={'Entities'}
                            columns={settingData.columns}
                            data={settingData.data}
                            options={{
                                defaultExpanded: false,
                                sorting: true,
                                actionsColumnIndex: -1
                            }}
                            actions={[
                                rowData => ({
                                    icon: () => <VisibilityIcon />,
                                    tooltip: 'View',
                                    onClick: (event, rowData) => {
                                        if (rowData.id !== attributes.organization) {
                                            history.push(`/entity/${rowData.id}`);
                                        } else {
                                            history.push(`/entity`);
                                        }
                                    }
                                })
                            ]}
                        />
                    </div> : ''}

                {
                    checkRole === 'Child Entity' ?
                        <ChildDetailedPage/> : ''
                }


                {
                    checkRole === 'Administrator' ?
                        <div style={{maxWidth: "100%"}}>
                            <MaterialTable
                                icons={tableIcons}
                                parentChildData={(row, rows) => rows.find(a => a.id === row.parentId)}
                                isLoading={componentLoading}
                                title={'Entities'}
                                columns={settingData.columns}
                                data={settingData.data}
                                options={{
                                    defaultExpanded: false,
                                    sorting: true,
                                    actionsColumnIndex: -1
                                }}
                                actions={[
                                    rowData => ({
                                        icon: () => <VisibilityIcon />,
                                        tooltip: 'View',
                                        onClick: (event, rowData) => {
                                            if (rowData.id !== attributes.organization) {
                                                history.push(`/entity/${rowData.id}`);
                                            } else {
                                                history.push(`/entity`);
                                            }
                                        }
                                    })
                                ]}
                            />
                        </div> : ''

                }

            </Layout>

        </>

    )
}


export default withOktaAuth(Dashboard);
