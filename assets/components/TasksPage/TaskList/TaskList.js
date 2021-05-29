
import React from 'react';
import Task from "../Task/Task";

const TaskList = ({data, children, events}) => {
    return (
        <div className="tasks">
            {children.map(task => {
                return <Task key={task.id} task={task} data={data} events={events}/>
            })}
        </div>
    );
}

export default TaskList;
