
import React from 'react';
import TaskHeader from "../TaskHeader/TaskHeader";
import TaskAdditionalPanel from "../TaskAdditionalPanel/TaskAdditionalPanel";
import TaskList from "../TaskList/TaskList";
import './Task.scss';

const Task = ({task, data, events}) => {
    const {tasks, statuses, nested} = data;
    const children = tasks.filter(e => e.parent === task.id);
    const showChildren = nested && task.isChildrenOpen && children.length > 0;
    return (
        <div className="task">
            <TaskHeader task={task} children={children} events={events}/>
            { task.isAdditionalPanelOpen ? <TaskAdditionalPanel task={task} statuses={statuses} events={events}/> : null }
            { showChildren ? <TaskList data={data} children={children} events={events} /> : null}
        </div>
    )
}

export default Task;
