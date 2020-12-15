import React from 'react';

import {makeStyles} from '@material-ui/core/styles';
import AppBar from '@material-ui/core/AppBar';
import CssBaseline from '@material-ui/core/CssBaseline';
import Drawer from '@material-ui/core/Drawer';
import IconButton from '@material-ui/core/IconButton';
import MoreIcon from '@material-ui/icons/MoreVert';
import Toolbar from '@material-ui/core/Toolbar';
import Typography from '@material-ui/core/Typography';

import ChatPanel from './ChatPanel';

const drawerWidth = 250;

const useStyles = makeStyles(theme => ({
    appBar: {
        width: `calc(100% - ${drawerWidth}px)`,
        marginLeft: drawerWidth,
    },

    sideColor: {
        backgroundColor: '#1e1e2d'
    },

    appBarTitle: {
        flexGrow: 1
    },
    drawerPaper: {
        width: drawerWidth
    },

    toolbar: theme.mixins.toolbar,
    content: {
        flexGrow: 1,
        padding: theme.spacing(3),
    },
}));

const MainChatApp = () => {
    const classes = useStyles();
    return (
        <React.Fragment>
            <CssBaseline/>
            <AppBar position="inherit" className={classes.appBar} color="default">
                <Toolbar>
                    <Typography variant="h6" noWrap>
                        #geheimorganisation
                    </Typography>
                </Toolbar>

            </AppBar>



            <Drawer
                className={'messageSideBar'}
                classes={{
                    paper: classes.drawerPaper
                }}
                variant="permanent"
                position="inherit"
            >
                <AppBar position="inherit" className={classes.sideColor}>
                    <Toolbar position="inherit">
                        <Typography className={classes.appBarTitle} variant="h6" noWrap>
                            Messages
                        </Typography>
                        <IconButton color="inherit" edge="end">
                            <MoreIcon/>
                        </IconButton>
                    </Toolbar>
                </AppBar>
                <ChatPanel/>
            </Drawer>
            {/*<main className={classes.content}>*/}
            {/*    <div className={classes.toolbar} />*/}
            {/*    <Typography paragraph>*/}
            {/*        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt*/}
            {/*        ut labore et dolore magna aliqua. Rhoncus dolor purus non enim praesent elementum*/}
            {/*        facilisis leo vel. Risus at ultrices mi tempus imperdiet. Semper risus in hendrerit*/}
            {/*        gravida rutrum quisque non tellus. Convallis convallis tellus id interdum velit laoreet id*/}
            {/*        donec ultrices. Odio morbi quis commodo odio aenean sed adipiscing. Amet nisl suscipit*/}
            {/*        adipiscing bibendum est ultricies integer quis. Cursus euismod quis viverra nibh cras.*/}
            {/*        Metus vulputate eu scelerisque felis imperdiet proin fermentum leo. Mauris commodo quis*/}
            {/*        imperdiet massa tincidunt. Cras tincidunt lobortis feugiat vivamus at augue. At augue eget*/}
            {/*        arcu dictum varius duis at consectetur lorem. Velit sed ullamcorper morbi tincidunt. Lorem*/}
            {/*        donec massa sapien faucibus et molestie ac.*/}
            {/*    </Typography>*/}
            {/*    <Typography paragraph>*/}
            {/*        Consequat mauris nunc congue nisi vitae suscipit. Fringilla est ullamcorper eget nulla*/}
            {/*        facilisi etiam dignissim diam. Pulvinar elementum integer enim neque volutpat ac*/}
            {/*        tincidunt. Ornare suspendisse sed nisi lacus sed viverra tellus. Purus sit amet volutpat*/}
            {/*        consequat mauris. Elementum eu facilisis sed odio morbi. Euismod lacinia at quis risus sed*/}
            {/*        vulputate odio. Morbi tincidunt ornare massa eget egestas purus viverra accumsan in. In*/}
            {/*        hendrerit gravida rutrum quisque non tellus orci ac. Pellentesque nec nam aliquam sem et*/}
            {/*        tortor. Habitant morbi tristique senectus et. Adipiscing elit duis tristique sollicitudin*/}
            {/*        nibh sit. Ornare aenean euismod elementum nisi quis eleifend. Commodo viverra maecenas*/}
            {/*        accumsan lacus vel facilisis. Nulla posuere sollicitudin aliquam ultrices sagittis orci a.*/}
            {/*    </Typography>*/}
            {/*</main>*/}

        </React.Fragment>
    );
};

export default MainChatApp;
