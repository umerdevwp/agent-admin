import React, {useContext} from 'react';
import MaterialTable from 'material-table';
import {makeStyles} from '@material-ui/core/styles';
// import {useHistory} from "react-router-dom";
import Grid from "@material-ui/core/Grid";
import {taskUpdate} from '../api/attachment';
import {UserContext} from "../context/UserContext";
import DialogTitle from "@material-ui/core/DialogTitle";
import DialogContent from "@material-ui/core/DialogContent";
import DialogContentText from "@material-ui/core/DialogContentText";
import DialogActions from "@material-ui/core/DialogActions";
import Button from "@material-ui/core/Button";
import Dialog from "@material-ui/core/Dialog";


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

function ComplianceTaskList(props) {
    localStorage.setItem('task', 'false');
    const {addError} = useContext(UserContext);

    const [loading, setLoading] = React.useState(false);
    const [open, setOpen] = React.useState(false);
    // const [userAgree, setUserAgree] = React.useState(false);
    const [status, setStatus] = React.useState('');

    // const [eid, setEid] = React.useState('');
    // const [data, setData] = React.useState(props.data)
    // const history = useHistory();
    // const classes = useStyles();


    // const data = [{id: "4071993000002295199", subject: "Dissolve LLC FQ", dueDate: "2019-12-12", status: "Not Started"}]
    const handleClose = () => {
        setOpen(false);
        setLoading(false);
        return null;
    };
    const taskUpdateController = () => {
        console.log('task',localStorage.getItem('task'))
        if(localStorage.getItem('task') === 'false'){
            setOpen(true);
        }


        if(localStorage.getItem('task') === 'true') {
            setOpen(false);

            let formData = new FormData();
            formData.append('status', status);
            formData.append('eid', props.eid);
            taskUpdate(props.eid, formData).then(response => {
                setLoading(false);
                if (response === 'success') {
                    props.update();
                }

                if (response.type === 'error') {
                    addError(response.message);
                }
            });
        }
    }

    const iAgree = async (event) => {

        localStorage.setItem('task', 'true');
        taskUpdateController();
    };


    return (

        <Grid item xs={12}>
            <Dialog
                open={open}
                onClose={handleClose}
                aria-labelledby="alert-dialog-title"
                aria-describedby="alert-dialog-description"
            >
                <DialogTitle id="alert-dialog-title">{"Compliance Task"}</DialogTitle>
                <DialogContent>
                    <DialogContentText id="alert-dialog-description">
                      Are you sure, you want to mark this task as "Complete"
                    </DialogContentText>
                </DialogContent>
                <DialogActions>
                    <Button onClick={handleClose} color="primary">
                        Cancel
                    </Button>
                    <Button onClick={(event) => iAgree(event)} color="primary" autoFocus>
                        Accept
                    </Button>
                </DialogActions>
            </Dialog>




            <MaterialTable
                isLoading={loading ? loading : props.loading}
                actions={[
                    {
                        icon: 'check',
                        tooltip: 'Mark this task as complete',
                        position: 'row',
                        onClick: (event, rowData) => {
                            setLoading(true);
                            Promise.resolve(setStatus(1));
                            setOpen(true);


                        }
                    }

                ]}
                title={props.title !== '' ? props.title : ''}
                options={{
                    selection: props.selection ? props.selection : false,
                    actionsColumnIndex: -1
                }}
                columns={props.data.columns}
                data={props.data.data}

            />
        </Grid>

    )
}


export default ComplianceTaskList;
