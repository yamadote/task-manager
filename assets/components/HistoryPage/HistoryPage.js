
import React, {useLayoutEffect, useState} from 'react';
import PanelHeading from "../Page/PanelHeading/PanelHeading";
import Helper from "../App/Helper";
import Config from "../App/Config";
import Page from "../Page/Page";
import PanelBody from "../Page/PanelBody/PanelBody";
import Icon from "../App/Icon";
import LocalStorage from "../App/LocalStorage";

const HistoryPage = () => {
    const title = "History";
    const icon = <Icon name="th-list"/>;

    const [search, setSearch] = useState("");
    const [reminderNumber, setReminderNumber] = useState(LocalStorage.getReminderNumber());

    const events = new function () {
        return {
            init: () => {
                Helper.fetchJson(Config.apiUrlPrefix + "/history")
                    .then(response => {
                        setReminderNumber(response.reminderNumber);
                        LocalStorage.setReminderNumber(reminderNumber);
                    });
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
            <PanelHeading title={title} icon={icon}/>
            <PanelBody>

            </PanelBody>
        </Page>
    );
}

export default HistoryPage;
