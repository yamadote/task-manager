
import React from 'react';
import './TaskHeader.scss';
import TaskAdditionalPanelViewButton from "./TaskAdditionalPanelViewButton/TaskAdditionalPanelViewButton";
import TaskChildrenViewButton from "./TaskChildrenViewButton/TaskChildrenViewButton";
import TaskTitle from "./TaskTitle/TaskTitle";
import TaskLink from "./TaskLink/TaskLink";
import TaskChildrenPageButton from "./TaskChildrenPageButton/TaskChildrenPageButton";

const TaskHeader = ({task, children, events}) => {
    const hasChildren = children.length > 0;
    return (
        <div className="task-header">
            { hasChildren ? <TaskChildrenViewButton task={task} events={events} /> : null }
            <TaskTitle task={task} events={events}/>
            { task.link ? <TaskLink link={task.link}/> : null }
            <TaskAdditionalPanelViewButton task={task} events={events}/>
            { hasChildren ? <TaskChildrenPageButton task={task}/> : null }
        </div>
    )
}

export default TaskHeader;
