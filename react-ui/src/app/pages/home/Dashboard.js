import React from "react";

import {metronic} from "../../../_metronic";
import EntityListing from '../entity/EntityListing';
import {withAuth} from '@okta/okta-react';
import {Title} from "./helpers/titles";

function Dashboard() {
    return (
        <>
            <Title title={'Dashboard'}/>
            <EntityListing title={'Entities'}/>
        </>
    );
};


export default withAuth(Dashboard);
