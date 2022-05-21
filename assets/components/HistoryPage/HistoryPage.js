
import React, {useLayoutEffect, useState} from 'react';
import PanelHeading from "../Page/PanelHeading/PanelHeading";
import Helper from "../App/Helper";
import Config from "../App/Config";
import Page from "../Page/Page";
import PanelBody from "../Page/PanelBody/PanelBody";
import Icon from "../App/Icon";
import Button from "../App/Button";
import LocalStorage from "../App/LocalStorage";
import ActionList from "./ActionList/ActionList";
import {useHistory} from "react-router-dom";

const HistoryPage = () => {
    const title = "History";
    const icon = <Icon name="th-list"/>;
    const history = useHistory();

    const [search, setSearch] = useState("");
    const [actions, setActions] = useState(undefined);
    const [reminderNumber, setReminderNumber] = useState(LocalStorage.getReminderNumber());

    const events = new function () {
        return {
            init: () => {
                Helper.fetchJson(Config.apiUrlPrefix + "/history")
                    .then(response => {
                        setActions(response.actions);
                        setReminderNumber(response.reminderNumber);
                        LocalStorage.setReminderNumber(reminderNumber);
                    });
            },
            reload: () => {
                setActions(undefined);
                events.init();
            },
            revealAction: (id) => {
                setActions((actions) => actions.map(action => {
                    if (action.id === id) {
                        action.revealed = true;
                    }
                    return action;
                }));
            },
            onSearchUpdate: () => {
                if (actions) {
                    setActions((actions) => actions.map(action => {
                        action.isHidden = !action.message.toLowerCase().includes(search.toLowerCase());
                        return action;
                    }));
                }
            }
        }
    }

    useLayoutEffect(events.init, []);
    useLayoutEffect(events.onSearchUpdate, [search]);

    return (
        <Page sidebar={{root: null, onSearch:setSearch, reminderNumber:reminderNumber}}>
            <PanelHeading title={title} icon={icon}>
                <div>
                    <Button onClick={events.reload}><span className="oi oi-reload"/></Button>
                    <Button onClick={history.goBack}><span className="oi oi-chevron-left"/></Button>
                </div>
            </PanelHeading>
            <PanelBody>
                <ActionList actions={actions} events={events} />
            </PanelBody>
        </Page>
    );
}

export default HistoryPage;
