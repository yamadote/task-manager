
import React from 'react';

const TaskChildrenViewButton = ({task, events}) => {
    const onButtonClick = () => events.updateTaskChildrenViewSetting(task.id, !task.isChildrenOpen);
    const iconClassName = task.isChildrenOpen ? "oi oi-chevron-top" : "oi oi-chevron-bottom";
    return (
        <button onClick={onButtonClick} className='title-button'>
            <span className={iconClassName}/>
        </button>
    );
}

export default TaskChildrenViewButton;
