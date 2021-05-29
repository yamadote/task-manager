
import React from 'react';

const TaskAdditionalPanel = ({task, events}) => {
    const onNewTaskButton = () => {events.createNewTask(task)};
    const onRemoveTaskButton= () => {events.removeTask(task)};
    return (
        <div className="mb-3">
            <button onClick={onNewTaskButton} className='btn btn-sm btn-secondary'>New Task</button>
            <button onClick={onRemoveTaskButton} className='btn btn-sm btn-danger'>Remove</button>
        </div>
    );
}

export default TaskAdditionalPanel;
