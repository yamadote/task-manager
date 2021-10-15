
import React from 'react';
import Task from "../Task/Task";

const TaskList = ({children, data, events}) => {
    return (
        <div className="tasks">
            {children.map(task => {
                return <Task key={task.id} task={task} data={data} events={events}/>
            })}
        </div>
    );
}

export default TaskList;
