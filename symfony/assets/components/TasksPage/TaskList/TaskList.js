
import React from 'react';
import Task from "../Task/Task";

const TaskList = (props) => {
    const children = props.tasks.filter(task => {
        return task.parent === props.parent
    });
    if (children.length === 0) {
        return null;
    }
    return (
        <div className="tasks">
            {children.map(task => {
                return <Task key={task.id} task={task} tasks={props.tasks}/>
            })}
        </div>
    );
}

export default TaskList;
