
import React from 'react';
import './TaskHeader.scss';
import TaskAdditionalPanelViewButton from "./TaskAdditionalPanelViewButton/TaskAdditionalPanelViewButton";
import TaskChildrenViewButton from "./TaskChildrenViewButton/TaskChildrenViewButton";
import TaskTitle from "./TaskTitle/TaskTitle";
import TaskLink from "./TaskLink/TaskLink";
import TaskChildrenPageButton from "./TaskChildrenPageButton/TaskChildrenPageButton";
import TaskTrackedTime from "./TaskTrackedTime/TaskTrackedTime";

const TaskHeader = ({task, children, events}) => {
    const hasChildren = children.length > 0;
    return (
        <div className="task-header">
            { hasChildren ? <TaskChildrenViewButton task={task} events={events} /> : null }
            <TaskTitle task={task} events={events}/>
            <TaskTrackedTime task={task} />
            { task.link ? <TaskLink link={task.link}/> : null }
            { hasChildren ? <TaskChildrenPageButton task={task}/> : null }
            <TaskAdditionalPanelViewButton task={task} events={events}/>
        </div>
    )
}

export default TaskHeader;
