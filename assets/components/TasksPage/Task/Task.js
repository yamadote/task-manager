
import React, {useState} from 'react';
import TaskList from "../TaskList/TaskList";
import './Task.scss';

const Task = (props) => {
    const task = props.task;
    const [isTitleChanging, setTitleChanging] = useState(false);
    const titleClassName = "title" + (isTitleChanging ? " changing" : "");

    const onTitleChange = event => props.events.updateTaskTitle(task.id, event.target.value, setTitleChanging);
    return (
        <div className="task">
            {!task.link ? null : <a href={task.link} target="_blank" className="float-right mt-2">link</a>}
            <input className={titleClassName} type="text" value={task.title} onChange={onTitleChange} />
            <div className="mb-3">
                <button onClick={() => {props.events.createNewTask(task)}}
                        className='btn btn-sm btn-secondary'>New Task</button>
                <button onClick={() => {props.events.removeTask(task)}}
                        className='btn btn-sm btn-danger'>Remove</button>
            </div>
            <TaskList tasks={props.tasks} parent={task.id} events={props.events} />
        </div>
    );
}

export default Task;
