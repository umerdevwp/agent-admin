import React, {useContext, useEffect} from 'react';
import MaterialTable from 'material-table';
import Skeleton from '@material-ui/lab/Skeleton';
import Link from '@material-ui/core/Link';
import Paper from '@material-ui/core/Paper';
import {makeStyles} from '@material-ui/core/styles';
import {
    withRouter,
    Redirect
} from 'react-router-dom';
import {useHistory} from "react-router-dom";
import VisibilityIcon from '@material-ui/icons/Visibility';
import Grid from "@material-ui/core/Grid";

import {OktaUserContext} from "../../context/OktaUserContext";
import {entityList, entityListingAxios} from "../../crud/enitity.crud";
import {fetchUserProfile} from "../../crud/auth.crud";
import EntityDetailedPage from "./EntityDetailedPage";

const useStyles = makeStyles(theme => ({
    root: {
        width: '100%',
        marginTop: theme.spacing(3),
        overflowX: 'auto',
    },
    table: {
        minWidth: 650,
    },
    //
    // "& span[data-index='0']": {
    //     transform: 'translateX(-15%)',
    // },


}));

function DatatableListing(props) {


    const settingData = {
        columns: [

            {title: 'Name', field: 'name', pointerEvents: "none"},
            {title: 'Entity Structure', field: 'entityStructure'},
            {title: 'Filing State', field: 'filingState'},
            {title: 'Formation Date', field: 'formationDate'},
        ],

        data: [
            {
                id: 1,
                name: 'Reset Marketing Solutions Inc',
                entityStructure: 'Corporation',
                filingState: 'WV',
                formationDate: '2018-06-30',
                cellStyle: {minWidth: 100}
            },

            {
                id: 2,
                name: ' Child 1 Reset Marketing Solutions Inc',
                entityStructure: 'Corporation',
                filingState: 'WV',
                formationDate: '2018-06-30',
                parentId: 1,
            },

            {
                id: 3,
                name: 'Child 2 Reset Marketing Solutions Inc',
                entityStructure: 'Corporation',
                filingState: 'WV',
                formationDate: '2018-06-30',
                parentId: 1,

            },

            {
                id: 4,
                name: 'Child 3 Reset Marketing Solutions Inc',
                entityStructure: 'Corporation',
                filingState: 'WV',
                formationDate: '2018-06-30',
                parentId: 3,

            },

            {
                id: 5,
                name: 'Triple Barrel Ranch, LLC',
                entityStructure: 'Corporation',
                filingState: 'WV',
                formationDate: '2018-06-30',
                parentId: 4,
            },
        ]


    };

    const handleUpdate = (newData) => {
        return Promise.resolve(console.log(newData));
    }
    return (
        <div style={{maxWidth: "100%"}}>
            <MaterialTable
                className={'sss'}
                parentChildData={(row, rows) => rows.find(a => a.id === row.parentId)}
                title={props.title !== '' ? props.title : ''}
                columns={settingData.columns}
                data={settingData.data}
                options={{
                    defaultExpanded: true,
                    childernStyle: {backgroundColor: "gray"}
                }}
            />
        </div>

    )
}


export default withRouter(DatatableListing);
