
import React, {useState} from 'react';
import './TaskAdditionalPanel.scss';

const TaskAdditionalPanel = ({task, events}) => {
    const [isLinkChanging, setLinkChanging] = useState(false);
    const linkClassName = isLinkChanging ? "changing" : "";
    const onLinkChange = event => events.updateTaskLink(task.id, event.target.value, setLinkChanging);

    const onNewTaskButton = () => {events.createNewTask(task)};
    const onRemoveTaskButton= () => {events.removeTask(task)};
    return (
        <div className="mb-3">
            <div className="field link">
                <span>Link:</span>
                <input type="text" value={task.link ?? ''} onChange={onLinkChange} className={linkClassName}/>
            </div>
            <button onClick={onNewTaskButton} className='btn btn-sm btn-secondary'>New Task</button>
            <button onClick={onRemoveTaskButton} className='btn btn-sm btn-danger'>Remove</button>
        </div>
    );
}

export default TaskAdditionalPanel;
