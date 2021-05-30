
import React from 'react';

const TaskStatusField = ({task, statuses, events}) => {
    const onStatusChange = event => events.updateTaskStatus(task.id, Number(event.target.value));
    const renderOptions = () => statuses.map(status => {
        const key = 'task-status-option' + status.id;
        return <option value={status.id} key={key}>{status.title}</option>
    });
    return (
        <div className="field status">
            <span>Status:</span>
            <select value={task.status} onChange={onStatusChange}>
                { renderOptions() }
            </select>
        </div>
    )
}

export default TaskStatusField;
