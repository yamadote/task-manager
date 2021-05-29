
import React, {useState} from 'react';
import './TaskTitle.scss';

const TaskTitle = ({task, children, events}) => {
    const [isTitleChanging, setTitleChanging] = useState(false);
    const titleClassName = "title" + (isTitleChanging ? " changing" : "");
    const onTitleChange = event => events.updateTaskTitle(task.id, event.target.value, setTitleChanging);

    const renderChildrenViewButton = () => {
        const onButtonClick = () => events.updateTaskChildrenViewSetting(task.id, !task.isChildrenOpen);
        const iconClassName = task.isChildrenOpen ? "oi oi-chevron-top" : "oi oi-chevron-bottom";
        return (
            <button onClick={onButtonClick} className='title-button mr-1'>
                <span className={iconClassName}/>
            </button>
        );
    }
    const renderLink = () => {
        return (
            <a href={task.link} target="_blank" className="title-link">
                <span className="oi oi-account-login"/>
            </a>
        );
    }
    const renderAdditionalPanelViewButton = () => {
        const onButtonClick = () => events.updateTaskAdditionalPanelViewSetting(task.id, !task.isAdditionalPanelOpen)
        const iconClassName = task.isAdditionalPanelOpen ? "oi oi-chevron-top" : "oi oi-chevron-bottom";
        return (
            <button onClick={onButtonClick} className='title-button'>
                <span className={iconClassName}/>
            </button>
        );
    }
    return (
        <div className="title-group">
            { children.length > 0 ? renderChildrenViewButton() : null }
            <input className={titleClassName} type="text" value={task.title} onChange={onTitleChange} />
            { task.link ? renderLink() : null }
            { renderAdditionalPanelViewButton() }
        </div>
    )
}

export default TaskTitle;
