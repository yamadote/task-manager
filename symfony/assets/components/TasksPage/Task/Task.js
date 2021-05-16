
import React from 'react';
import TaskList from "../TaskList/TaskList";
import './Task.scss';

const Task = (props) => {
    const link = props.task.link;
    return (
        <div className="task">
            {!link ? null : <a href={link} target="_blank" className="float-right mt-2">link</a>}
            <div className="pt-2 pb-2">{props.task.title}</div>
            <div className="mb-3">
                <button onClick={() => {props.events.createNewTask(props.task)}}
                        className='btn btn-sm btn-secondary'>New Task</button>
                <button onClick={() => {props.events.removeTask(props.task)}}
                        className='btn btn-sm btn-danger'>Remove</button>
            </div>
            <TaskList tasks={props.tasks} parent={props.task.id} events={props.events} />
        </div>
    );
}

export default Task;
