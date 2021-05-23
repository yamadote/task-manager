
import React, {useState} from 'react';
import {BrowserRouter as Router, Switch, Link, Route} from "react-router-dom";
import './App.scss';
import TasksPage from "../TasksPage/TasksPage";

const App = () => {
    const [renderTaskPage, events] = prepareTaskPageHandlers()
    return (
        <Router>
            <nav className="navbar justify-content-between">
                <ul className="nav nav-tabs">
                    <li className="nav-item dropdown nav-item-tasks">
                        <a className="nav-link dropdown-toggle"
                           href="#" id="navbarDropdownMenuLink" data-toggle="dropdown"
                           aria-haspopup="true" aria-expanded="false">
                            Tasks
                        </a>
                        <div className="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <Link to="/tasks/status/frozen" className="dropdown-item">Frozen</Link>
                            <Link to="/tasks/status/potential" className="dropdown-item">Potential</Link>
                            <Link to="/tasks/status/cancelled" className="dropdown-item">Cancelled</Link>
                            <Link to="/tasks/status/completed" className="dropdown-item">Completed</Link>
                            <Link to="/tasks" className="dropdown-item">All</Link>
                        </div>
                    </li>
                    <li className="nav-item nav-item-todo">
                        <Link to="/tasks/todo" className="nav-link">Todo</Link>
                    </li>
                    <li className="nav-item nav-item-reminders">
                        <Link to="/tasks/reminders" className="nav-link">Reminders</Link>
                    </li>
                    <li className="nav-item nav-item-progress">
                        <Link to="/tasks/status/progress" className="nav-link">In Progress</Link>
                    </li>
                </ul>
                <ul className="nav nav-tabs">
                    <li className="nav-item nav-item-new-task">
                        <button className="nav-link" onClick={events.createNewTask}>New Task</button>
                    </li>
                    <li className="nav-item">
                        <a href="/logout" className="nav-link">Logout</a>
                    </li>
                </ul>
            </nav>
            <Switch>
                {renderTaskPage("/tasks/reminders", "/api/tasks/reminders", false)}
                {renderTaskPage("/tasks/todo", "/api/tasks/todo")}
                {renderTaskPage("/tasks/status/progress", "/api/tasks/status/progress", false)}
                {renderTaskPage("/tasks/status/frozen", "/api/tasks/status/frozen")}
                {renderTaskPage("/tasks/status/potential", "/api/tasks/status/potential")}
                {renderTaskPage("/tasks/status/cancelled", "/api/tasks/status/cancelled")}
                {renderTaskPage("/tasks/status/completed", "/api/tasks/status/completed")}
                {renderTaskPage("/", "/api/tasks")}
            </Switch>
        </Router>
    );
}

let timeoutStorage = {};
const addTimeout = (id, func, timeout = 1000) => {
    clearTimeout(timeoutStorage[id]);
    timeoutStorage[id] = setTimeout(func, timeout);
}

const fetchJsonPost = (url, body) => {
    return fetch(url, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(body)
    }).then(response => response.json())
}

const prepareTaskPageHandlers = () => {
    const [tasks, setTasks] = useState(undefined);
    const createNewTask = (parent = null) => {
        fetchJsonPost('/api/tasks/new', {'parent': parent?.id})
            .then(task => setTasks(tasks => [task, ...tasks]))
    }
    const updateTask = (id, update) => {
        setTasks(tasks => {
            return tasks.map(task => {
                if (task.id === id) {
                    task = update(task);
                }
                return task;
            });
        })
    }
    const updateTaskTitle = (id, title) => {
        updateTask(id, (task) => {
            task.title = title;
            return task;
        })
        addTimeout('task_title' + id, () => {
            fetchJsonPost('/api/tasks/' + id + '/edit', {'title': title})
                .then(task => setTasks(tasks => [task, ...tasks]))
        });
    }
    const removeTask = (task) => {
        fetch('/api/tasks/' + task.id + '/delete', {method: 'POST'})
            .then(() => {
                // todo: remove task children
                setTasks(tasks => tasks.filter(i => i.id !== task.id))
            })
    }
    const events = {
        'createNewTask': createNewTask,
        'removeTask': removeTask,
        'updateTaskTitle': updateTaskTitle
    }
    const renderTaskPage = (path, fetchFrom, nested = true) => {
        return (
            <Route path={path}>
                <TasksPage
                    fetchFrom={fetchFrom}
                    nested={nested}
                    tasks={tasks}
                    init={(url) => {
                        setTasks(undefined);
                        fetch(url)
                            .then(response => response.json())
                            .then(tasks => setTasks(tasks));
                    }}
                    events={events}
                />
            </Route>
        )
    }
    return [renderTaskPage, events];
}

export default App;
