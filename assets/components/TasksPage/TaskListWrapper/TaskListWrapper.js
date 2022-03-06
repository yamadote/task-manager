
import React from 'react';
import TaskList from '../TaskList/TaskList'

const TaskListWrapper = ({data, events}) => {
    const {tasks, root, nested} = data;
    if (tasks === undefined) {
        return null;
    }
    const getChildren = () => {
        if (nested === false) {
            return tasks;
        }
        const rootId = root?.id || null;
        return tasks.filter(task => task.parent === rootId);
    }
    const children = getChildren();
    if (children.length === 0) {
        return null;
    }
    return <TaskList children={children} data={data} events={events}/>;
}

export default TaskListWrapper;
