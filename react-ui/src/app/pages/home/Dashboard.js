import React, {useMemo} from "react";
import {useSelector} from "react-redux";
import {
    Portlet,
    PortletBody,
    PortletHeader,
    PortletHeaderToolbar
} from "../../partials/content/Portlet";
import {metronic} from "../../../_metronic";
import QuickStatsChart from "../../widgets/QuickStatsChart";
import OrderStatisticsChart from "../../widgets/OrderStatisticsChart";
import OrdersWidget from "../../widgets/OrdersWidget";
import SalesBarChart from "../../widgets/SalesBarChart";
import DownloadFiles from "../../widgets/DownloadFiles";
import NewUsers from "../../widgets/NewUsers";
import LatestUpdates from "../../widgets/LatestUpdates";
import BestSellers from "../../widgets/BestSellers";
import RecentActivities from "../../widgets/RecentActivities";
import PortletHeaderDropdown from "../../partials/content/CustomDropdowns/PortletHeaderDropdown";
import TablesExamplesPages from '../home/google-material/data-displays/TablesExamplesPage';
import EntityListing from '../entity/EntityListing';
import {withAuth} from '@okta/okta-react';
import Container from "@material-ui/core/Container";
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
