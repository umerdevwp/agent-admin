import React from 'react';

import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';
import ListItemText from '@material-ui/core/ListItemText';
import ListSubheader from '@material-ui/core/ListSubheader';

const ChatPanel = () => (
    <React.Fragment>
        <List
            dense
            subheader={<ListSubheader>Message Box</ListSubheader>}
            style={{ padding: 0 }}
        >
            <ListItem button>
                <ListItemText primary="hackint" />
            </ListItem>
            <ListItem button>
                <ListItemText primary="freenode" />
            </ListItem>
        </List>
        <List dense subheader={<ListSubheader>irc.hackint.org</ListSubheader>}>
            <ListItem button>
                <ListItemText primary="#geheimorganisation" />
            </ListItem>
            <ListItem button>
                <ListItemText primary="#ccchh" />
            </ListItem>
            <ListItem button>
                <ListItemText primary="#ffhh" />
            </ListItem>
        </List>
    </React.Fragment>
);

export default ChatPanel;
