
import React from 'react';
import TaskTitle from "../TaskTitle/TaskTitle";
import TaskAdditionalPanel from "../TaskAdditionalPanel/TaskAdditionalPanel";
import TaskList from "../TaskList/TaskList";
import './Task.scss';

const Task = ({task, tasks, nested, events}) => {
    const children = tasks.filter(e => e.parent === task.id);
    const showChildren = nested && task.isChildrenOpen
    return (
        <div className="task">
            <TaskTitle task={task} children={children} events={events}/>
            { task.isAdditionalPanelOpen ? <TaskAdditionalPanel task={task} events={events}/> : null }
            { showChildren ? <TaskList tasks={tasks} children={children} nested={nested} events={events} /> : null}
        </div>
    )
}

export default Task;
