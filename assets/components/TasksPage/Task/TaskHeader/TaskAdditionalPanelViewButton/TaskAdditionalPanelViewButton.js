
import React from 'react';

const TaskAdditionalPanelViewButton = ({task, events}) => {
    const onButtonClick = () => events.updateTaskAdditionalPanelViewSetting(task.id, !task.isAdditionalPanelOpen)
    const iconClassName = task.isAdditionalPanelOpen ? "oi oi-chevron-top" : "oi oi-chevron-bottom";
    return (
        <button onClick={onButtonClick} className='title-button'>
            <span className={iconClassName}/>
        </button>
    );
}

export default TaskAdditionalPanelViewButton;
