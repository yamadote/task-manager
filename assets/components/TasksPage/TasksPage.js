
import React, {useEffect} from 'react';
import TaskList from './TaskList/TaskList'

const TasksPage = (props) => {
    const tasks = props.tasks;
    useEffect(() => {
        props.init(props.fetchFrom);
    }, [props.fetchFrom]);

    if (tasks === undefined) {
        return "loading ...";
    }
    if (tasks.length === 0) {
        return "no records found";
    }
    const parent = tasks.find(task => task.parent === null);
    return (<TaskList tasks={tasks} parent={parent.id} nested={props.nested} events={props.events}/>);
}

export default TasksPage;
