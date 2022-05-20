
import React from 'react';
import Action from "../Action/Action";
import TableSpacer from "./TableSpacer";
import moment from "moment";
import Config from "../../App/Config";
import RepeatedAction from "../Action/RepeatedAction";

const ActionList = ({actions, events}) => {
    if (!actions) {
        return null;
    }
    const isRepeatedAction = (action, previousAction) => {
        return !action.revealed
            && previousAction
            && previousAction.type === action.type
            && previousAction.task.id === action.task.id
            && Config.repeatedActionTypes.includes(action.type);
    }
    const isSameActionTask = (action, previousAction) => {
        return previousAction
            && previousAction.task.id === action.task.id;
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
    let repeatedActionAmount = 0;
    actions.forEach(action => {
        if (action.isHidden) {
            return;
        }
        const spacer = resolveSpacer(action, previousAction);
        if (spacer) {
            list.push(spacer);
        }
        if (!spacer && isRepeatedAction(action, previousAction)) {
            if (repeatedActionAmount < Config.repeatedActionMaxAmount) {
                list.push(<RepeatedAction key={action.id} action={action} events={events} />);
                repeatedActionAmount += 1;
            }
        } else {
            const isMergedTaskColumn = !spacer && !repeatedActionAmount && isSameActionTask(action, previousAction);
            list.push(<Action key={action.id} action={action} isMergedTaskColumn={isMergedTaskColumn} />);
            repeatedActionAmount = 0;
        }
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
