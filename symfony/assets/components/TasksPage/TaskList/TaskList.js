
import React from 'react';
import Task from "../Task/Task";

const TaskList = (props) => {
    return (
        <div className="tasks">
            {props.tasks.map((task) => {
                return <Task key={task.id} title={task.title}/>
            })}
        </div>
    );
}

export default TaskList;
