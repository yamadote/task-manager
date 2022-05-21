
import React from 'react';
import moment from "moment";
import './Action.scss';
import ActionMessage from "./ActionMessage/ActionMessage";

const Action = ({action, isMergedTaskColumn}) => {
    const getStatusClassName = () => {
        if (action.type === 'createTask') {
            return 'info';
        }
        if (action.type === 'editTaskStatus') {
            return 'warning';
        }
        return '';
    }
    const statusClassName = getStatusClassName();
    const time = moment.unix(action.createdAt).format('HH:mm');
    const task = isMergedTaskColumn ? null : action.task.title;
    return (
        <tr>
            <td className={"column time-column " + statusClassName}>
                <div className="column-content">{time}</div>
            </td>
            <td className={"column message-column " + statusClassName}>
                <ActionMessage message={action.message} />
            </td>
            <td className={"column task-column " + (isMergedTaskColumn ? 'merged-column' : '')}>
                <div className="column-content">{task}</div>
            </td>
        </tr>
    );
}

export default Action;
