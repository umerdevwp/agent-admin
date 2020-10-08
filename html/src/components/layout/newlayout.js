import React, {useContext} from 'react';
import clsx from 'clsx';
import {makeStyles} from '@material-ui/core/styles';
import {useTheme} from '@material-ui/core/styles';
import CssBaseline from '@material-ui/core/CssBaseline';
import Drawer from '@material-ui/core/Drawer';
import AppBar from '@material-ui/core/AppBar';
import Toolbar from '@material-ui/core/Toolbar';
import List from '@material-ui/core/List';
import Typography from '@material-ui/core/Typography';
import Divider from '@material-ui/core/Divider';
import IconButton from '@material-ui/core/IconButton';
import Badge from '@material-ui/core/Badge';
import Container from '@material-ui/core/Container';
import MenuIcon from '@material-ui/icons/Menu';
import ChevronLeftIcon from '@material-ui/icons/ChevronLeft';
import NotificationsIcon from '@material-ui/icons/Notifications';
import MainListItems from '../routes/listItems';
import useMediaQuery from '@material-ui/core/useMediaQuery';
import Footer from './Footer';
import Box from "@material-ui/core/Box";
import {UserConsumer} from "../context/UserContext";
import Button from '@material-ui/core/Button';
import Menu from '@material-ui/core/Menu';
import MenuItem from '@material-ui/core/MenuItem';
import Fade from '@material-ui/core/Fade';
import Avatar from '@material-ui/core/Avatar';
import Loader from 'react-loader';
import {SnackbarProvider} from 'notistack';
// import Loader from 'react-loader-spinner'
import UserContextProvider from "../context/UserContext";

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

export default function Layout(props) {


    const theme = useTheme();
    const classes = useStyles();
    const matches = useMediaQuery(theme.breakpoints.up('sm'));
    const [open, setOpen] = React.useState(true);

    React.useEffect(() => {

        if (matches) {
            setOpen(true);
        } else {
            setOpen(false);
        }
    }, [matches])
    const handleDrawerOpen = () => {
        setOpen(true);
    };
    const handleDrawerClose = () => {
        setOpen(false);
    };

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

        <UserConsumer>
            {value => (
                <Loader loaded={value.loading}>
                    <SnackbarProvider maxSnack={3}>
                        <div className={classes.root}>
                            <CssBaseline/>
                            <AppBar position="absolute"
                                    className={clsx(classes.appBar, open && classes.appBarShift)}>

                                <Toolbar className={classes.toolbar}>
                                    <IconButton
                                        edge="start"
                                        color="white"
                                        aria-label="open drawer"
                                        onClick={handleDrawerOpen}
                                        className={clsx(classes.menuButton, open && classes.menuButtonHidden)}
                                    >

                                        <MenuIcon color="white"/>
                                    </IconButton>
                                    <Typography component="h1" variant="h6" noWrap className={classes.title}>
                                        {value.title}
                                    </Typography>
                                    <IconButton color="inherit">
                                        <Badge badgeContent={4} color="secondary">
                                            <NotificationsIcon/>
                                        </Badge>
                                    </IconButton>
                                    <div>
                                        <Button aria-controls="fade-menu" aria-haspopup="true"
                                                onClick={handleClick}>
                                            <Avatar className={classes.purple}>OP</Avatar>
                                        </Button>
                                        <Menu
                                            id="menu-list-grow"
                                            anchorEl={anchorEl}
                                            keepMounted
                                            // anchorOrigin={{ vertical: "bottom", horizontal: "center" }}
                                            // transformOrigin={{ vertical: "top", horizontal: "center" }}
                                            open={openMenu}
                                            onClose={handleClose}
                                            TransitionComponent={Fade}
                                        >
                                            <MenuItem onClick={handleClose}>Profile</MenuItem>
                                            <MenuItem onClick={handleClose}>My account</MenuItem>
                                            <MenuItem onClick={handleClose}>Logout</MenuItem>
                                        </Menu>
                                    </div>

                                </Toolbar>
                            </AppBar>
                            <Drawer
                                variant="permanent"
                                classes={{
                                    paper: clsx(classes.drawerPaper, !open && classes.drawerPaperClose),
                                }}
                                open={open}
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
                                <List className={classes.listColor}><MainListItems/></List>

                            </Drawer>
                            <main className={classes.content}>
                                <div className={classes.appBarSpacer}/>
                                <Container maxWidth="lg" className={classes.container}>
                                    {value.loading === true ?
                                        props.children : null }

                                    <Box pt={4}>
                                        <Footer/>
                                    </Box>
                                </Container>
                            </main>
                        </div>
                    </SnackbarProvider>

                </Loader>
            )}
        </UserConsumer>


    );
}
