
import React from 'react';
import TaskHeader from "./TaskHeader/TaskHeader";
import TaskAdditionalPanel from "./TaskAdditionalPanel/TaskAdditionalPanel";
import TaskList from "../TaskList/TaskList";
import TaskStatusBadge from "./TaskStatusBadge/TaskStatusBadge";
import moment from "moment";
import './Task.scss';

const Task = ({task, data, events}) => {
    const {tasks, activeTask, activeTaskStatus, statuses, nested} = data;
    const isActive = activeTask && activeTask.task === task.id;
    const children = tasks.filter(e => e.parent === task.id);
    const showChildren = nested && task.isChildrenOpen && children.length > 0;

    const isReminder = task.reminder && task.reminder < moment().unix();
    const badgeStatusId = isActive ? activeTaskStatus : task.status;
    const badgeStatus = statuses.find((status) => status.id === badgeStatusId);
    return (
        <div className="task">
            <TaskStatusBadge isReminder={isReminder} status={badgeStatus}/>
            <TaskHeader task={task} children={children} events={events}/>
            { task.isAdditionalPanelOpen ? <TaskAdditionalPanel
                task={task} isActive={isActive} statuses={statuses} events={events}/> : null }
            { showChildren ? <TaskList children={children} data={data} events={events} /> : null}
        </div>
    )
}

export default Task;
