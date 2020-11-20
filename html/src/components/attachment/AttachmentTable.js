import React from 'react';
import MaterialTable from 'material-table';

import {makeStyles} from '@material-ui/core/styles';
import {
    withRouter
} from 'react-router-dom';
import {useHistory} from "react-router-dom";

import Grid from "@material-ui/core/Grid";
import PictureAsPdfIcon from "@material-ui/icons/PictureAsPdf";
import CloudDownloadIcon from '@material-ui/icons/CloudDownload';


const useStyles = makeStyles(theme => ({
    root: {
        width: '100%',
        marginTop: theme.spacing(3),
        overflowX: 'auto',
    },
    table: {
        minWidth: 650,
    },
}));

function AttachmentTable(props) {
    const [loading, setLoading] = React.useState(false);
    const history = useHistory();

    return (

        <Grid item xs={12}>
            <MaterialTable
                isLoading={loading ? loading : props.loading}
                actions={[
                    rowData => ({
                        icon: () => <CloudDownloadIcon/>,
                        tooltip: 'Download',
                        onClick: (event, rowData) => {
                            const url = `${process.env.REACT_APP_SERVER_API_URL}/download/${rowData.fileId}?token=${rowData.token}&name=${rowData.name}`;
                            window.open(url,'_blank');
                        }
                    }),
                    {
                        icon: 'add',
                        tooltip: props.tooltip ? props.tooltip : 'Add User',
                        isFreeAction: true,
                        onClick: (event) => {
                            if (props.redirect) {
                                history.push(props.url);
                            }
                        }
                    }
                ]}
                options={{
                    actionsColumnIndex: -1,
                    sorting: true,
                    search: true
                }}
                title={props.title !== '' ? props.title : ''}
                columns={props.data.columns}
                data={props.data.data}

            />
        </Grid>

    )
}
export default withRouter(AttachmentTable);
