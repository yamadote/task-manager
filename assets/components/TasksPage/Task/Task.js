
import React, {useEffect} from 'react';
import TaskHeader from "./TaskHeader/TaskHeader";
import TaskAdditionalPanel from "./TaskAdditionalPanel/TaskAdditionalPanel";
import TaskList from "../TaskList/TaskList";
import TaskStatusBadge from "./TaskStatusBadge/TaskStatusBadge";
import moment from "moment";
import './Task.scss';

const Task = ({task, data, events, increaseParentTrackedTime}) => {
    const {tasks, activeTask, statuses, nested} = data;
    const isActive = activeTask && activeTask.task === task.id;
    const children = tasks.filter(e => e.parent === task.id);
    const showChildren = nested && task.isChildrenOpen && children.length > 0;

    const isReminder = task.reminder && task.reminder < moment().unix();
    const status = statuses.find((status) => status.id === task.status);
    const currentTime = Math.floor(Date.now() / 1000);

    const increaseTrackedTime = (value = 1) => {
        events.updateTaskTrackedTime(task.id, task.trackedTime + value);
        increaseParentTrackedTime(value);
    }
    const increaseChildrenTrackedTime = (value = 1) => {
        events.updateTaskChildrenTrackedTime(task.id, task.childrenTrackedTime + value);
        increaseParentTrackedTime(value);
    }

    useEffect(() => {
        if (isActive) {
            increaseTrackedTime(currentTime - activeTask.startedAt);
        }
    }, []);
    useEffect(() => {
        if (isActive) {
            let myInterval = setInterval(increaseTrackedTime, 1000);
            return () => clearInterval(myInterval);
        }
    });

    return (
        <div className="task">
            <TaskStatusBadge isReminder={isReminder} isActive={isActive} status={status}/>
            <TaskHeader task={task} children={children} events={events}/>
            { task.isAdditionalPanelOpen ? <TaskAdditionalPanel task={task} isActive={isActive} statuses={statuses} events={events}/> : null }
            { showChildren ? <TaskList children={children} data={data} events={events} increaseParentTrackedTime={increaseChildrenTrackedTime}/> : null}
        </div>
    )
}

export default Task;
