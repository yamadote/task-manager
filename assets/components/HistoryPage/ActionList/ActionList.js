
import React from 'react';
import Action from "../Action/Action";
import TableSpacer from "./TableSpacer";
import moment from "moment";
import Config from "../../App/Config";

const ActionList = ({actions}) => {
    if (!actions) {
        return null;
    }
    let list = [];
    let previousDate = null;
    let previousTime = null;
    actions.forEach(action => {
        if (action.isHidden) {
            return;
        }
        const date = moment.unix(action.createdAt).format('MMMM DD dddd');
        if (previousDate !== date) {
            list.push(<TableSpacer content={date} />);
        } else if (previousTime && ((previousTime - action.createdAt) > Config.historyActionSpacerTime)) {
            list.push(<TableSpacer />);
        }
        previousDate = date;
        previousTime = action.createdAt;
        list.push(<Action key={action.id} action={action} />);
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
