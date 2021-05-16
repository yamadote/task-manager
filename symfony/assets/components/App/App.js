
import React, {useState} from 'react';
import {BrowserRouter as Router, Switch, Route, Link} from "react-router-dom";
import TasksPage from "../TasksPage/TasksPage";
import './App.scss';

const App = () => {
    const [tasks, setTasks] = useState(undefined);
    const createNewTask = () => {
        console.log(window.location.pathname);
    }
    const initTasksPage = (url) => {
        setTasks(undefined);
        fetch(url)
            .then(response => response.json())
            .then(tasks => setTasks(tasks));
    }
    const renderTaskPage = (path, fetchFrom, nested = true) => {
        return (
            <Route path={path}>
                <TasksPage fetchFrom={fetchFrom} nested={nested} tasks={tasks} init={initTasksPage}/>
            </Route>
        )
    }

    return (
        <Router>
            <nav className="main-nav navbar justify-content-between">
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
                        <button className="nav-link" onClick={createNewTask}>New Task</button>
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

export default App;
