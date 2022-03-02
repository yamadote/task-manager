
import React, {useState} from 'react';
import TaskStatusField from "./TaskStatusField/TaskStatusField";
import TaskLinkField from "./TaskLinkField/TaskLinkField";
import TaskReminderField from "./TaskReminderField/TaskReminderField";
import './TaskAdditionalPanel.scss';
import moment from "moment";
import TaskDescriptionEditor from "./TaskDescriptionEditor/TaskDescriptionEditor";
import TaskTimeTrackingButton from "./TaskTimeTrackingButton/TaskTimeTrackingButton"

const TaskAdditionalPanel = ({task, isActive, statuses, events}) => {
    const [isDescriptionHidden, setDescriptionHidden] = useState(!task.description)
    return (
        <div className="additional-panel">
            <div className="fields">
                <TaskStatusField task={task} statuses={statuses} events={events} />
                <TaskReminderField task={task} events={events} />
                <TaskLinkField task={task} events={events} />
            </div>
            <TaskTimeTrackingButton task={task} isActive={isActive} events={events}/>
            <button onClick={() => events.createNewTask(task.id)} className='btn btn-sm btn-secondary'>New Task</button>
            <button onClick={() => events.removeTask(task.id)} className='btn btn-sm btn-danger'>Remove</button>
            <button onClick={() => setDescriptionHidden(false)} className='btn btn-sm btn-info'>Description</button>
            <span className="created-at">{moment.unix(task.createdAt).format('DD/MM/YYYY HH:mm')}</span>
            { isDescriptionHidden ? null : <TaskDescriptionEditor task={task} events={events}/> }
        </div>
    );
}

export default TaskAdditionalPanel;
