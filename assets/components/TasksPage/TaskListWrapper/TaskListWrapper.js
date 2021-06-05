
import React from 'react';
import TaskList from '../TaskList/TaskList'

const TaskListWrapper = ({data, events}) => {
    const {tasks, root, nested} = data;
    if (tasks === undefined) {
        return "loading ..."
    }
    if (tasks.length === 0) {
        return "no records found";
    }
    const getChildren = () => {
        if (nested === false) {
            return tasks;
        }
        const rootId = root?.id || null;
        return tasks.filter(task => task.parent === rootId);
    }
    return <TaskList children={getChildren()} data={data} events={events}/>;
}

export default TaskListWrapper;
