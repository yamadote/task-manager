
import React, {useLayoutEffect, useState} from 'react';
import PanelHeading from "../Page/PanelHeading/PanelHeading";
import Helper from "../App/Helper";
import Config from "../App/Config";
import Page from "../Page/Page";
import PanelBody from "../Page/PanelBody/PanelBody";

const SettingsPage = () => {

    const [search, setSearch] = useState("");
    const [reminderNumber, setReminderNumber] = useState(undefined);

    const events = new function () {
        return {
            init: () => {
                Helper.fetchJson(Config.apiUrlPrefix + "/settings")
                    .then(response => setReminderNumber(response.reminderNumber));
            },
            onSearchUpdate: () => {
                console.log("TODO SEARCH: " + search);
            }
        }
    }

    useLayoutEffect(events.init, []);
    useLayoutEffect(events.onSearchUpdate, [search]);

    return (
        <Page sidebar={{root: null, onSearch:setSearch, reminderNumber:reminderNumber}}>
            <PanelHeading title={<span><i className="glyphicon glyphicon-cog" />Settings</span>}/>
            <PanelBody>

            </PanelBody>
        </Page>
    );
}

export default SettingsPage;
