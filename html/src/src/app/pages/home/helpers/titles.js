import React from 'react';
import {
    Portlet,
    PortletBody,
    PortletHeader,
    PortletHeaderToolbar
} from "../../../partials/content/Portlet";
import PortletHeaderDropdown from "../../../partials/content/CustomDropdowns/PortletHeaderDropdown";


export function Title(props) {

    return (
        <div>
            <Portlet fluidHeight={true}>
                <PortletHeader title={props.title} />
            </Portlet>
        </div>
    );
}
