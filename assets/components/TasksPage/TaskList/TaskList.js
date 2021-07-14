
import React from 'react';
import Task from "../Task/Task";

const TaskList = ({children, data, events, increaseParentTrackedTime}) => {
    return (
        <div className="tasks">
            {children.map(task => {
                return <Task key={task.id} task={task} data={data} events={events} increaseParentTrackedTime={increaseParentTrackedTime}/>
            })}
        </div>
    );
}

export default TaskList;
