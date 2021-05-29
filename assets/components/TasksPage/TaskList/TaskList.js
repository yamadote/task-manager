
import React from 'react';
import Task from "../Task/Task";

const TaskList = (props) => {
    if (props.children.length === 0) {
        return null;
    }
    return (
        <div className="tasks">
            {props.children.map(task => {
                return <Task key={task.id} task={task} tasks={props.tasks} events={props.events}/>
            })}
        </div>
    );
}

export default TaskList;
