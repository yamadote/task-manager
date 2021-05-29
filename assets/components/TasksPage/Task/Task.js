
import React from 'react';
import TaskTitle from "../TaskTitle/TaskTitle";
import TaskList from "../TaskList/TaskList";
import './Task.scss';

const Task = (props) => {
    const task = props.task;
    const children = props.tasks.filter(task => {
        return task.parent === props.task.id;
    });
    const displayAdditionalPanel = () => {
        const onNewTaskButton = () => {props.events.createNewTask(task)};
        const onRemoveTaskButton= () => {props.events.removeTask(task)};
        return (
            <div className="mb-3">
                <button onClick={onNewTaskButton} className='btn btn-sm btn-secondary'>New Task</button>
                <button onClick={onRemoveTaskButton} className='btn btn-sm btn-danger'>Remove</button>
            </div>
        );
    };
    const displayChildren = () => {
        return (<TaskList tasks={props.tasks} children={children} events={props.events} />);
    }
    return (
        <div className="task">
            <TaskTitle task={task} children={children} events={props.events}/>
            { task.isAdditionalPanelOpen ? displayAdditionalPanel() : null }
            { task.isChildrenOpen ? displayChildren() : null}
        </div>
    )
}

export default Task;
