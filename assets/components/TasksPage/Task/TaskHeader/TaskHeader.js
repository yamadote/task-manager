
import React from 'react';
import './TaskHeader.scss';
import TaskAdditionalPanelViewButton from "./TaskAdditionalPanelViewButton/TaskAdditionalPanelViewButton";
import TaskChildrenViewButton from "./TaskChildrenViewButton/TaskChildrenViewButton";
import TaskTitle from "./TaskTitle/TaskTitle";
import TaskLink from "./TaskLink/TaskLink";

const TaskHeader = ({task, children, events}) => {
    return (
        <div className="task-header">
            { children.length > 0 ? <TaskChildrenViewButton task={task} events={events} /> : null }
            <TaskTitle task={task} events={events}/>
            { task.link ? <TaskLink link={task.link}/> : null }
            <TaskAdditionalPanelViewButton task={task} events={events}/>
        </div>
    )
}

export default TaskHeader;
