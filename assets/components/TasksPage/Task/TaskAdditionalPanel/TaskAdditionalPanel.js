
import React, {useState} from 'react';
import TaskStatusField from "./TaskStatusField/TaskStatusField";
import TaskLinkField from "./TaskLinkField/TaskLinkField";
import TaskReminderField from "./TaskReminderField/TaskReminderField";
import './TaskAdditionalPanel.scss';
import moment from "moment";
import TaskDescriptionEditor from "./TaskDescriptionEditor/TaskDescriptionEditor";
import TaskTimeTrackingButton from "./TaskTimeTrackingButton/TaskTimeTrackingButton"
import Button from "../../../App/Button";

const TaskAdditionalPanel = ({task, isActive, statuses, events}) => {
    const [isDescriptionHidden, setDescriptionHidden] = useState(!task.description)
    return (
        <div className="additional-panel">
            <div className="fields">
                <TaskStatusField task={task} statuses={statuses} events={events} />
                <TaskReminderField task={task} events={events} />
                <TaskLinkField task={task} events={events} />
            </div>
            <div className="additional-panel-buttons">
                <TaskTimeTrackingButton task={task} isActive={isActive} events={events}/>
                <Button onClick={() => events.createNewTask(task.id)} buttonStyle='secondary' buttonSize='sm'>New Task</Button>
                <Button onClick={() => events.removeTask(task.id)} buttonStyle='danger' buttonSize='sm'>Remove</Button>
                <Button onClick={() => setDescriptionHidden(false)} buttonStyle='info' buttonSize='sm'>Description</Button>
                <span className="created-at">{moment.unix(task.createdAt).format('DD/MM/YYYY HH:mm')}</span>
            </div>
            { isDescriptionHidden ? null : <TaskDescriptionEditor task={task} events={events}/> }
        </div>
    );
}

export default TaskAdditionalPanel;
