
import React from 'react';
import OpenIcon from "../../../../App/OpenIcon";

const TaskAdditionalPanelViewButton = ({task, events}) => {
    const onButtonClick = () => events.updateTaskAdditionalPanelViewSetting(task.id, !task.isAdditionalPanelOpen);
    const iconName = task.isAdditionalPanelOpen ? "chevron-top" : "chevron-bottom";
    return (
        <button onClick={onButtonClick} className='title-button'>
            <OpenIcon name={iconName}/>
        </button>
    );
}

export default TaskAdditionalPanelViewButton;
