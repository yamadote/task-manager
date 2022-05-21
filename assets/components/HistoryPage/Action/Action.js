
import React from 'react';
import moment from "moment";
import './Action.scss';
import ActionMessage from "./ActionMessage/ActionMessage";
import ActionTask from "./ActionTask/ActionTask";

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
    return (
        <tr>
            <td className={"column time-column " + statusClassName}>
                <div className="column-content">{time}</div>
            </td>
            <td className={"column message-column " + statusClassName}>
                <ActionMessage message={action.message} />
            </td>
            <td className={"column task-column " + (isMergedTaskColumn ? 'merged-column' : '')}>
                { !isMergedTaskColumn ? <ActionTask task={action.task} /> : null }
            </td>
        </tr>
    );
}

export default Action;
