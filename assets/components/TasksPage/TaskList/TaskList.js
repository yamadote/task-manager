
import React from 'react';
import Task from "../Task/Task";

const TaskList = (props) => {
    let children = props.tasks;
    if (props.nested !== false) {
        children = children.filter(task => {
            return task.parent === props.parent
        });
    }
    if (children.length === 0) {
        return null;
    }
    return (
        <div className="tasks">
            {children.map(task => {
                return <Task key={task.id} task={task} tasks={props.tasks} events={props.events}/>
            })}
        </div>
    );
}

export default TaskList;
