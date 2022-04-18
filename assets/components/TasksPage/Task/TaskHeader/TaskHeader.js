
import React from 'react';
import './TaskHeader.scss';
import TaskAdditionalPanelViewButton from "./TaskAdditionalPanelViewButton/TaskAdditionalPanelViewButton";
import TaskChildrenViewButton from "./TaskChildrenViewButton/TaskChildrenViewButton";
import TaskTitle from "./TaskTitle/TaskTitle";
import TaskLink from "./TaskLink/TaskLink";
import TaskChildrenPageButton from "./TaskChildrenPageButton/TaskChildrenPageButton";
import TaskTrackedTime from "./TaskTrackedTime/TaskTrackedTime";
import TaskGithubLink from "./TaskGithubLink/TaskGithubLink";

const TaskHeader = ({task, activeTask, children, events}) => {
    const hasChildren = children.length > 0;
    return (
        <div className="task-header">
            { hasChildren ? <TaskChildrenViewButton task={task} events={events} /> : null }
            { task.link ? <TaskGithubLink link={task.link}/> : null }
            <TaskTitle task={task} events={events}/>
            <TaskTrackedTime task={task} activeTask={activeTask}/>
            { task.link ? <TaskLink link={task.link}/> : null }
            { hasChildren ? <TaskChildrenPageButton task={task}/> : null }
            <TaskAdditionalPanelViewButton task={task} events={events}/>
        </div>
    )
}

export default TaskHeader;
