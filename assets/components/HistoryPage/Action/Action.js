
import React from 'react';
import moment from "moment";

const Action = ({action}) => {
    let className = '';
    if (action.type === 'createTask') {
        className = 'info';
    }
    if (action.type === 'editTaskStatus') {
        className = 'warning';
    }
    const time = moment.unix(action.createdAt).format('MMM DD HH:mm');
    return (
        <tr className={className}>
            <td className="column time-column">{time}</td>
            <td className="column message-column">{action.message}</td>
            <td className="column task-column"><div className="title">{action.task}</div></td>
        </tr>
    );
}

export default Action;
