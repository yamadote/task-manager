
import React from 'react';
import './TaskStatusBadge.scss';

const TaskStatusBadge = ({isReminder, status}) => {
    const statusBadgeColor = isReminder ? 'rgb(255, 99, 71)' : status.color;
    return (
        <div className="status-badge">
            <div style={{borderTopColor: statusBadgeColor}}/>
        </div>
    )
}

export default TaskStatusBadge;
