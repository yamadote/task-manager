
import React from 'react';
import TaskHeader from "./TaskHeader/TaskHeader";
import TaskAdditionalPanel from "./TaskAdditionalPanel/TaskAdditionalPanel";
import TaskList from "../TaskList/TaskList";
import './Task.scss';
import TaskStatusBadge from "./TaskStatusBadge/TaskStatusBadge";

const Task = ({task, data, events}) => {
    const {tasks, activeTask, statuses, nested} = data;
    const isActive = activeTask && activeTask.task === task.id;
    const children = tasks.filter(e => e.parent === task.id);
    const showChildren = nested && task.isChildrenOpen && children.length > 0;
    return (
        <div className="task">
            <TaskStatusBadge task={task} statuses={statuses}/>
            <TaskHeader task={task} children={children} events={events}/>
            { task.isAdditionalPanelOpen ? <TaskAdditionalPanel
                task={task} isActive={isActive} statuses={statuses} events={events}/> : null }
            { showChildren ? <TaskList children={children} data={data} events={events} /> : null}
        </div>
    )
}

export default Task;
