
import React from 'react';
import './TasksAmount.scss';

const TasksAmount = ({tasks, root, nested}) => {
    if (!tasks) {
        return null;
    }
    const count = (tasks, parent) => {
        let amount = 0;
        tasks.forEach(task => {
            if (!task.isHidden && task.parent === parent) {
                amount += count(tasks, task.id) + 1;
            }
        });
        return amount;
    }
    let amount = root && nested ? count(tasks, root.id) : tasks.length;
    if (amount === 0) {
        return null;
    }
    return (
        <div className='task-amount'>Tasks Amount: {amount}</div>
    );
}

export default TasksAmount;
