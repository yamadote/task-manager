
import React, {useState, useEffect} from 'react';
import TaskList from './TaskList/TaskList'

const TasksPage = (props) => {
    const [tasks, setTasks] = useState(undefined);
    useEffect(() => {
        setTasks(undefined);
        fetch(props.url)
            .then(response => response.json())
            .then(tasks => setTasks(tasks));
    }, [props.url]);

    if (tasks === undefined) {
        return "loading";
    }
    if (tasks.length === 0) {
        return "no records found";
    }
    const parent = tasks.find(task => task.parent === null);
    return (<TaskList tasks={tasks} parent={parent.id} nested={props.nested}/>);
}

export default TasksPage;
