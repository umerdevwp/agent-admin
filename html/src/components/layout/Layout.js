import React, {useContext, useRef} from 'react';
import {useMediaQuery} from 'react-responsive'
import clsx from 'clsx';
import {makeStyles} from '@material-ui/core/styles';
import CssBaseline from '@material-ui/core/CssBaseline';
import Drawer from '@material-ui/core/Drawer';
import AppBar from '@material-ui/core/AppBar';
import Toolbar from '@material-ui/core/Toolbar';
import List from '@material-ui/core/List';
import Typography from '@material-ui/core/Typography';
import Divider from '@material-ui/core/Divider';
import IconButton from '@material-ui/core/IconButton';
import Container from '@material-ui/core/Container';
import MenuIcon from '@material-ui/icons/Menu';
import ChevronLeftIcon from '@material-ui/icons/ChevronLeft';
import MainListItems from '../routes/listItems';
import ChildListItems from '../routes/ChildListItems';
import ParentListItems from '../routes/ParentListItems';
import Footer from './Footer';
import Box from "@material-ui/core/Box";
import {UserContext} from "../context/UserContext";
import Menu from '@material-ui/core/Menu';
import MenuItem from '@material-ui/core/MenuItem';
import Fade from '@material-ui/core/Fade';
import {useHistory} from "react-router-dom";
import {SnackbarProvider} from 'notistack';
// import {createMuiTheme} from '@material-ui/core/styles';
import {useOktaAuth, withOktaAuth} from "@okta/okta-react";

import {SemipolarLoading} from 'react-loadingg';
import {ToastContainer} from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

const drawerWidth = 240;


const useStyles = makeStyles((theme) => ({
    root: {
        display: 'flex',
    },
    toolbar: {
        paddingRight: 24, // keep right padding when drawer closed
        backgroundColor: '#1e1e2d'
    },
    toolbarIcon: {
        // color: '#434d6b',
        // backgroundColor: '#434d6b',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'flex-end',
        padding: '0 8px',
        ...theme.mixins.toolbar,
    },
    appBar: {
        zIndex: theme.zIndex.drawer + 1,
        transition: theme.transitions.create(['width', 'margin'], {
            easing: theme.transitions.easing.sharp,
            duration: theme.transitions.duration.leavingScreen,
        }),
    },
    appBarShift: {
        marginLeft: drawerWidth,
        width: `calc(100% - ${drawerWidth}px)`,
        transition: theme.transitions.create(['width', 'margin'], {
            easing: theme.transitions.easing.sharp,
            duration: theme.transitions.duration.enteringScreen,
        }),
    },
    menuButton: {
        color: '#ffffff',
        // backgroundColor: '#ffffff',
        marginRight: 36,
    },
    menuButtonHidden: {
        display: 'none',
    },

    chevronLeftIcon: {
        color: '#ffffff'
    },

    title: {
        marginLeft: 10,
        flexGrow: 1,
        color: 'white'
    },

    button: {
        textTransform: "none",
        fontSize: 14
    },
    drawerPaper: {

        backgroundColor: '#1e1e2d',
        position: 'relative',
        whiteSpace: 'nowrap',
        width: drawerWidth,
        transition: theme.transitions.create('width', {
            easing: theme.transitions.easing.sharp,
            duration: theme.transitions.duration.enteringScreen,
        }),
    },

    drawer: {
        [theme.breakpoints.up('sm')]: {
            width: drawerWidth,
            flexShrink: 0,
        },
    },

    drawerPaperClose: {
        overflowX: 'hidden',
        transition: theme.transitions.create('width', {
            easing: theme.transitions.easing.sharp,
            duration: theme.transitions.duration.leavingScreen,
        }),
        width: theme.spacing(7),
        [theme.breakpoints.up('sm')]: {
            width: theme.spacing(9),
        },
    },
    appBarSpacer: theme.mixins.toolbar,
    content: {
        flexGrow: 1,
        height: '100vh',
        overflow: 'auto',
        backgroundColor: '#f0f0f6'
    },
    container: {
        paddingTop: theme.spacing(4),
        paddingBottom: theme.spacing(4),
    },
    paper: {
        padding: theme.spacing(2),
        display: 'flex',
        overflow: 'auto',
        flexDirection: 'column',
    },
    fixedHeight: {
        height: 240,
    },

    listColor: {
        color: "#ffffff"
    }


}));

export default withOktaAuth(function Layout(props) {
    const {authState} = useOktaAuth();
    const {role, title, drawerState, changeDrawer, profile} = useContext(UserContext);
    let timer;
    let flag = 1;
    // const theme = useTheme();
    const classes = useStyles();
    const matches = useMediaQuery({maxWidth: 767});
    // const matches = useMediaQuery(theme.breakpoints.up('sm'));
    const history = useHistory();

    React.useEffect(() => {
        // if (!matches) {
        //     changeDrawer(true);
        // }
        if (matches) {
            changeDrawer(false);
        }
    }, [matches]);
    const timerToClearSomewhere = useRef();


    const ClearSession = () => {
        localStorage.clear();
        props.authService.logout('/');
    }


    React.useEffect(() => {
        if (typeof role === "undefined") {
            timerToClearSomewhere.current = setTimeout(function () {
                ClearSession()
            }, 40000);
        } else {
            clearTimeout(timerToClearSomewhere.current);
        }

    }, [role]);


    const handleDrawerOpen = () => {
        changeDrawer(true);
    };
    const handleDrawerClose = () => {
        changeDrawer(false);
    };


    // const OKTAClearanceFunction = setTimeout(() => {
    //             if(flag === 1) {
    //                 localStorage.clear();
    //                 props.authService.logout('/');
    //             }
    //     }, 40000);
    //
    //
    // const myStopFunction = () => {
    //     flag = 2;
    //     console.log('timeout');
    //     console.log(flag)
    //     clearTimeout(OKTAClearanceFunction);
    // }

    const [mobileOpen, setMobileOpen] = React.useState(false);

    const handleDrawerToggle = () => {
        setMobileOpen(!mobileOpen);
    };
    const [anchorEl, setAnchorEl] = React.useState(null);
    const openMenu = Boolean(anchorEl);

    const handleClick = (event) => {
        setAnchorEl(event.currentTarget);
    };

    const handleClose = () => {
        setAnchorEl(null);
    };


    return (
        <>
            {role ?
                <>

                    <SnackbarProvider maxSnack={3}>
                        <div className={classes.root}>
                            <CssBaseline/>
                            <AppBar position="absolute"
                                    className={clsx(classes.appBar, drawerState && classes.appBarShift)}>

                                <Toolbar className={classes.toolbar}>
                                    <IconButton
                                        edge="start"
                                        aria-label="open drawer"
                                        onClick={handleDrawerOpen}
                                        className={clsx(classes.menuButton, drawerState && classes.menuButtonHidden)}
                                    >

                                        <MenuIcon/>
                                    </IconButton>
                                    <Typography component="h1" variant="h6" noWrap className={classes.title}>
                                        {title}
                                    </Typography>
                                    {/*<IconButton color="inherit">*/}
                                    {/*    <Badge badgeContent={4} color="secondary">*/}
                                    {/*        <NotificationsIcon/>*/}
                                    {/*    </Badge>*/}
                                    {/*</IconButton>*/}

                                    <IconButton color="inherit" onClick={handleClick}>
                                        <Typography variant="h5" className={clsx(classes.title, classes.button)}>
                                            Hi, {profile.name ? profile.name : ''}
                                        </Typography>
                                    </IconButton>
                                    <Menu
                                        anchorOrigin={{
                                            vertical: "bottom",
                                            horizontal: "right"
                                        }}
                                        id="menu-list-grow"
                                        anchorEl={anchorEl}
                                        keepMounted
                                        open={openMenu}
                                        onClose={handleClose}
                                        TransitionComponent={Fade}
                                        getContentAnchorEl={null}
                                    >
                                        <MenuItem onClick={() => {
                                            props.authService.logout('/');
                                        }}>Logout</MenuItem>
                                    </Menu>


                                </Toolbar>
                            </AppBar>
                            <Drawer
                                variant="permanent"
                                classes={{
                                    paper: clsx(classes.drawerPaper, !drawerState && classes.drawerPaperClose),
                                }}
                                open={drawerState}
                                onClose={handleDrawerToggle}
                                ModalProps={{
                                    keepMounted: true, // Better open performance on mobile.
                                }}
                            >
                                <div className={classes.toolbarIcon}>
                                    <Typography component="h3" variant="h6" color="inherit" noWrap
                                                className={classes.title}>
                                        AgentAdmin
                                    </Typography>
                                    <IconButton onClick={handleDrawerClose}>
                                        <ChevronLeftIcon className={classes.chevronLeftIcon}/>
                                    </IconButton>
                                </div>
                                <Divider/>
                                {role === 'Administrator' ?
                                    <List className={classes.listColor}><MainListItems/></List> : ''}

                                {role === 'Child Entity' ?
                                    <List className={classes.listColor}><ChildListItems/></List> : ''}

                                {role === 'Parent Organization' ?
                                    <List className={classes.listColor}><ParentListItems/></List> : ''}

                            </Drawer>
                            <main className={classes.content}>
                                <div className={classes.appBarSpacer}/>
                                <Container maxWidth="lg" className={classes.container}>

                                    {props.children}

                                    <Box pt={4}>
                                        <Footer/>
                                    </Box>
                                </Container>
                            </main>
                        </div>
                    </SnackbarProvider>
                    <ToastContainer/>
                </>
                : <><SemipolarLoading className={'Test'} size={'large'} color={'#1e1e2d'}/></>}

        </>

    );
});
