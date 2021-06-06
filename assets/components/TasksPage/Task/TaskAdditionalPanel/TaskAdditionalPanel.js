
import React from 'react';
import TaskStatusField from "./TaskStatusField/TaskStatusField";
import TaskLinkField from "./TaskLinkField/TaskLinkField";
import TaskReminderField from "./TaskReminderField/TaskReminderField";
import './TaskAdditionalPanel.scss';
import moment from "moment";

const TaskAdditionalPanel = ({task, statuses, events}) => {
    return (
        <div className="mb-3">
            <div className="fields">
                <TaskStatusField task={task} statuses={statuses} events={events} />
                <TaskReminderField task={task} events={events} />
                <TaskLinkField task={task} events={events} />
            </div>
            <button onClick={() => {events.startTask(task.id)}} className='btn btn-sm btn-primary'>Start</button>
            <button onClick={() => {events.finishTask(task.id)}} className='btn btn-sm btn-info'>Finish</button>
            <button onClick={() => {events.createNewTask(task.id)}} className='btn btn-sm btn-secondary'>New Task</button>
            <button onClick={() => {events.removeTask(task.id)}} className='btn btn-sm btn-danger'>Remove</button>
            <span className="created-at">{moment.unix(task.createdAt).format('DD/MM/YYYY HH:mm')}</span>
        </div>
    );
}

export default TaskAdditionalPanel;
