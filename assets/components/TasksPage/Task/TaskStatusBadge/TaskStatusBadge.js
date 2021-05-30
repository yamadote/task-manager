
import React from 'react';
import './TaskStatusBadge.scss';
import moment from "moment";

const TaskStatusBadge = ({task, statuses}) => {
    const status = statuses.find((status) => status.id === task.status);
    const hasReminder = task.reminder && task.reminder < moment().unix();
    const statusBadgeColor = hasReminder ? 'rgb(255, 99, 71)' : status.color;
    return (
        <div className="status-badge">
            <div style={{borderTopColor: statusBadgeColor}}/>
        </div>
    )
}

export default TaskStatusBadge;
