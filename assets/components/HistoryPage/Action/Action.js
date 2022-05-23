
import React from 'react';
import './Action.scss';
import ActionMessage from "./ActionMessage/ActionMessage";
import ActionTask from "./ActionTask/ActionTask";
import ActionTime from "./ActionTime/ActionTime";

const Action = ({action, isMergedTaskColumn}) => {
    let statusClassName = '';
    if (action.type === 'createTask') {
        statusClassName = 'info';
    }
    if (action.type === 'editTaskStatus') {
        statusClassName = 'warning';
    }
    return (
        <tr>
            <td className={"column time-column " + statusClassName}>
                <ActionTime timestamp={action.createdAt} />
            </td>
            <td className={"column message-column " + statusClassName}>
                <ActionMessage message={action.message} />
            </td>
            <td className={"column task-column " + (isMergedTaskColumn ? 'merged-column' : '')}>
                { action.task && !isMergedTaskColumn ? <ActionTask task={action.task} /> : null }
            </td>
        </tr>
    );
}

export default Action;
