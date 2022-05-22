
import React, {useLayoutEffect, useState} from 'react';
import Helper from "../App/Helper";
import Config from "../App/Config";
import Page from "../Page/Page";
import PanelBody from "../Page/PanelBody/PanelBody";
import Icon from "../App/Icon";
import LocalStorage from "../App/LocalStorage";
import ActionList from "./ActionList/ActionList";
import {useParams} from "react-router-dom";
import HistoryPanelHeading from "./HistoryPanelHeading/HistoryPanelHeading";

const HistoryPage = () => {
    const title = "History";
    const icon = <Icon name="th-list"/>;
    const params = useParams();

    const getTaskInitialState = (params) => params.task ? {'id': parseInt(params.task)} : undefined;
    const [task, setTask] = useState(getTaskInitialState(params))
    const [search, setSearch] = useState("");
    const [actions, setActions] = useState(undefined);
    const [reminderNumber, setReminderNumber] = useState(LocalStorage.getReminderNumber());

    const events = new function () {
        return {
            init: () => {
                Helper.fetchJson(Config.apiUrlPrefix + "/history", {'task': params.task})
                    .then(response => {
                        setTask(response.task);
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

    useLayoutEffect(events.init, [params.task]);
    useLayoutEffect(events.onSearchUpdate, [search]);

    return (
        <Page sidebar={{root: null, onSearch:setSearch, reminderNumber:reminderNumber}}>
            <HistoryPanelHeading title={title} icon={icon} task={task} events={events} />
            <PanelBody>
                <ActionList actions={actions} events={events} />
            </PanelBody>
        </Page>
    );
}

export default HistoryPage;
