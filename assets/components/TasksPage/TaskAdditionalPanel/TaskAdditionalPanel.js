
import React, {useState} from 'react';
import './TaskAdditionalPanel.scss';

const TaskAdditionalPanel = ({task, events}) => {
    const renderLinkField = () => {
        const [isLinkChanging, setLinkChanging] = useState(false);
        const linkClassName = isLinkChanging ? "changing" : "";
        const onLinkChange = event => events.updateTaskLink(task.id, event.target.value, setLinkChanging);
        return (
            <div className="field link">
                <span>Link:</span>
                <input type="text" value={task.link ?? ''} onChange={onLinkChange} className={linkClassName}/>
            </div>
        );
    }
    return (
        <div className="mb-3">
            { renderLinkField() }
            <button onClick={() => {events.createNewTask(task)}} className='btn btn-sm btn-secondary'>New Task</button>
            <button onClick={() => {events.removeTask(task)}} className='btn btn-sm btn-danger'>Remove</button>
        </div>
    );
}

export default TaskAdditionalPanel;
