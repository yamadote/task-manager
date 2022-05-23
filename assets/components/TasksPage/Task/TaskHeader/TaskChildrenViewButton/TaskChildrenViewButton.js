
import React from 'react';
import OpenIcon from "../../../../App/OpenIcon";

const TaskChildrenViewButton = ({task, events}) => {
    const onButtonClick = () => events.updateTaskChildrenViewSetting(task.id, !task.isChildrenOpen);
    const iconName = task.isChildrenOpen ? "chevron-top" : "chevron-bottom";
    return (
        <button onClick={onButtonClick} className='title-button'>
            <OpenIcon name={iconName}/>
        </button>
    );
}

export default TaskChildrenViewButton;
