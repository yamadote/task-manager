
import React from 'react';
import Action from "../Action/Action";
import TableSpacer from "./TableSpacer";
import moment from "moment";
import Config from "../../App/Config";

const ActionList = ({actions}) => {
    if (!actions) {
        return null;
    }
    const prepareDate = (timestamp) => {
        return moment.unix(timestamp).format('MMMM DD dddd');
    }
    const renderSpacer = (action, content = null) => {
        return <TableSpacer key={"spacer-" + action.id} content={content} />;
    }
    const resolveSpacer = (action, previousAction) => {
        const date = prepareDate(action.createdAt);
        if (!previousAction) {
            return renderSpacer(action, date);
        }
        const previousDate = prepareDate(previousAction.createdAt);
        if (date !== previousDate) {
            return renderSpacer(action, date);
        }
        const diff = previousAction.createdAt - action.createdAt;
        if (Config.historyActionSpacerTime < diff) {
            return renderSpacer(action);
        }
        return null;
    }
    let list = [];
    let previousAction = null;
    actions.forEach(action => {
        if (action.isHidden) {
            return;
        }
        list.push(resolveSpacer(action, previousAction));
        list.push(<Action key={action.id} action={action} />);
        previousAction = action;
    })
    return (
        <div className="table-responsive">
        <table className="table table-bordered history-action-list">
            <tbody>{list}</tbody>
        </table>
        </div>
    );
}

export default ActionList;
