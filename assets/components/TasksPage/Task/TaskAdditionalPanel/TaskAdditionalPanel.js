
import React, {useState} from 'react';
import TaskStatusField from "./TaskStatusField/TaskStatusField";
import TaskLinkField from "./TaskLinkField/TaskLinkField";
import TaskReminderField from "./TaskReminderField/TaskReminderField";
import './TaskAdditionalPanel.scss';
import moment from "moment";
import TaskDescriptionEditor from "./TaskDescriptionEditor/TaskDescriptionEditor";
import TaskTimeTrackingButton from "./TaskTimeTrackingButton/TaskTimeTrackingButton"
import Button from "../../../App/Button";
import OpenIcon from "../../../App/OpenIcon";
import {Link} from "react-router-dom";
import Helper from "../../../App/Helper";

const TaskAdditionalPanel = ({task, isActive, statuses, events}) => {
    const [isDescriptionHidden, setDescriptionHidden] = useState(!task.description)

    const onNewTaskClick = () => events.createNewTask(task.id);
    const onDescriptionClick = () => setDescriptionHidden(false);
    const onRemoveTaskClick = () => events.removeTask(task.id);
    const createdAt = moment.unix(task.createdAt).format('DD/MM/YYYY HH:mm');

    return (
        <div className="additional-panel">
            <div className="fields">
                <TaskStatusField task={task} statuses={statuses} events={events} />
                <TaskReminderField task={task} events={events} />
                <TaskLinkField task={task} events={events} />
            </div>
            <div className="additional-panel-buttons">
                <TaskTimeTrackingButton task={task} isActive={isActive} events={events}/>
                <Button onClick={onNewTaskClick} buttonStyle='secondary' buttonSize='sm'>New Task</Button>
                <Button onClick={onDescriptionClick} buttonStyle='info' buttonSize='sm'>Description</Button>
                <span className="created-at">{createdAt}</span>
                <Link to={Helper.getHistoryPageUrl(task)}>
                    <Button className="right-side-button" buttonSize='sm'><OpenIcon name="clock"/></Button>
                </Link>
                <Button className="right-side-button" onClick={onRemoveTaskClick} buttonSize='sm'><OpenIcon name="trash"/></Button>
            </div>
            { isDescriptionHidden ? null : <TaskDescriptionEditor task={task} events={events}/> }
        </div>
    );
}

export default TaskAdditionalPanel;
