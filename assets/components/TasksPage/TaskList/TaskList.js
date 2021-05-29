
import React from 'react';
import Task from "../Task/Task";

const TaskList = ({tasks, children, nested, events}) => {
    if (children.length === 0) {
        return null;
    }
    return (
        <div className="tasks">
            {children.map(task => <Task key={task.id} task={task} tasks={tasks} nested={nested} events={events}/>)}
        </div>
    );
}

export default TaskList;
