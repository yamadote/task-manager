
import React, {useState} from 'react';
import './TaskTitle.scss';

const TaskTitle = ({task, children, events}) => {
    const [isTitleChanging, setTitleChanging] = useState(false);
    const titleClassName = "title" + (isTitleChanging ? " changing" : "");
    const onTitleChange = event => events.updateTaskTitle(task.id, event.target.value, setTitleChanging);

    const displayChildrenViewButton = () => {
        const onChildrenViewButton = () => events.updateTaskChildrenViewSetting(task.id);
        const iconClassName = task.isChildrenOpen ? "oi oi-chevron-top" : "oi oi-chevron-bottom";
        return (
            <button onClick={onChildrenViewButton} className='title-button mr-1'>
                <span className={iconClassName}/>
            </button>
        );
    }
    const displayLink = () => {
        return (
            <a href={task.link} target="_blank" className="title-link">
                <span className="oi oi-account-login"/>
            </a>
        );
    }
    const displayAdditionalPanel = () => {
        const onAdditionalPanelViewButton = () => events.updateTaskAdditionalPanelViewSetting(task.id)
        const iconClassName = task.isAdditionalPanelOpen ? "oi oi-chevron-top" : "oi oi-chevron-bottom";
        return (
            <button onClick={onAdditionalPanelViewButton} className='title-button'>
                <span className={iconClassName}/>
            </button>
        );
    }
    return (
        <div className="title-group">
            { children.length > 0 ? displayChildrenViewButton() : null }
            <input className={titleClassName} type="text" value={task.title} onChange={onTitleChange} />
            { task.link ? displayLink() : null }
            { displayAdditionalPanel() }
        </div>
    )
}

export default TaskTitle;
