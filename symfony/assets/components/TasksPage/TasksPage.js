
import React, {useState, useEffect} from 'react';
import TaskList from './TaskList/TaskList'

const TasksPage = () => {
    const [tasks, setTasks] = useState([]);

    useEffect(() => {
        fetch('/api/task')
            .then(response => response.json())
            .then(tasks => {
                setTasks(tasks);
            });
    }, []);

    return (
        <TaskList tasks={tasks} />
    );
}

export default TasksPage;
