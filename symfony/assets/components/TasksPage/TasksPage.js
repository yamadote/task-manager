
import React, {useEffect} from 'react';
import TaskList from './TaskList/TaskList'

const TasksPage = (props) => {
    useEffect(() => {
        props.init(props.fetchFrom);
    }, [props.fetchFrom]);

    if (props.tasks === undefined) {
        return "loading ...";
    }
    if (props.tasks.length === 0) {
        return "no records found";
    }
    const parent = props.tasks.find(task => task.parent === null);
    return (<TaskList tasks={props.tasks} parent={parent.id} nested={props.nested}/>);
}

export default TasksPage;
