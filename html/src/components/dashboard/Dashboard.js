import React, {useEffect, useContext, forwardRef} from 'react';
// import MaterialTable, {MTableToolbar} from "material-table";
import MaterialTable from "@material-table/core";
import Layout from "../layout/Layout";
import {entityList, StateRegionList} from "../api/enitity.crud";
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


const columns = [

    {title: 'Name', field: 'name'},
    {title: 'Entity Structure', field: 'entityStructure'},
    {title: 'Filing State', field: 'filingState'},
    {title: 'Formation Date', field: 'formationDate'},
];


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

const constPathColors = {
    1: '#e8e8e8',
    2: '#dcdcdc',
    3: '#cfcfcf',
    4: '#c2c2c2',
    5: '#b6b6b6'
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


const Dashboard = (props, {onChange, ...rest}) => {
    const classes = useStyles();
    const history = useHistory();
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
        try {

            const tokenResult = await props.authState.accessToken;
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
                    <MaterialTable
                        isLoading={componentLoading}
                        title="Entities"
                        data={entitydata ? entitydata : ''}
                        columns={columns}
                        parentChildData={(row, rows) => rows.find(a => a.id === row.parentId)}
                        options={{
                            // selection: true,
                            // paging: false,
                            sorting: true,
                            search: true,


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
                        // onSelectionChange={rows =>
                        //     onChange(
                        //         rows.filter(onlyUnique).map(({id, value}) => ({id, value}))
                        //     )
                        // }
                    /> : ''}

                {
                    checkRole === 'Child Entity' ?
                        <ChildDetailedPage/> : ''
                }

                {checkRole === 'Administrator' ?
                    <MaterialTable
                        isLoading={componentLoading}
                        title="Entities"
                        data={entitydata ? entitydata : ''}
                        columns={columns}
                        parentChildData={(row, rows) => rows.find(a => a.id === row.parentId)}
                        options={{
                            // selection: true,
                            // paging: false,
                            sorting: true,
                            search: true,


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
                        // onSelectionChange={rows =>
                        //     onChange(
                        //         rows.filter(onlyUnique).map(({id, value}) => ({id, value}))
                        //     )
                        // }
                    /> : ''}

            </Layout>
        </>
    );
}

export default withOktaAuth(Dashboard);
