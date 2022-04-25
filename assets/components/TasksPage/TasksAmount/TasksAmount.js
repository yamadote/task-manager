
import React from 'react';
import './TasksAmount.scss';

const TasksAmount = ({tasks}) => {
    let amount = tasks ? tasks.filter(task => !task.isHidden).length : 0;
    if (!amount) {
        return null;
    }
    return (
        <div className='task-amount'>Tasks Amount: {amount}</div>
    );
}

export default TasksAmount;
