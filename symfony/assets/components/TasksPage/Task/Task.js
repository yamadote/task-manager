
import React from 'react';
import TaskList from "../TaskList/TaskList";

const Task = (props) => {
    return (
        <div className="task">
            <div>Title: {props.task.title}</div>
            <TaskList tasks={props.tasks} parent={props.task.id} />
        </div>
    );
}

export default Task;
