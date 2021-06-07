
import React from 'react';
import './TaskStatusBadge.scss';

const TaskStatusBadge = ({isReminder, isActive, status}) => {
    let color = status.color;
    if (isActive) {
        color = '#ffb6c1';
    }
    if (isReminder) {
        color = 'rgb(255, 99, 71)';
    }
    return (
        <div className="status-badge">
            <div style={{borderTopColor: color}}/>
        </div>
    )
}

export default TaskStatusBadge;
