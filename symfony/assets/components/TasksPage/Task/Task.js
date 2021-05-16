
import React from 'react';
import TaskList from "../TaskList/TaskList";
import './Task.scss';

const Task = (props) => {
    const link = props.task.link;
    return (
        <div className="task">
            {!link ? null : <a href={link} target="_blank" className="float-right mt-2">link</a>}
            <div className="pt-2 pb-2">Title: {props.task.title}</div>
            <TaskList tasks={props.tasks} parent={props.task.id} />
        </div>
    );
}

export default Task;
