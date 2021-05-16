
import React from 'react';
import {BrowserRouter as Router, Switch, Route, Link} from "react-router-dom";
import TasksPage from "../TasksPage/TasksPage";
import './App.scss';

const App = () => {
    return (
        <Router>
            <nav>
                <ul className="nav nav-tabs">
                    <li className="nav-item dropdown">
                        <a className="nav-link dropdown-toggle"
                           href="#" id="navbarDropdownMenuLink" data-toggle="dropdown"
                           aria-haspopup="true" aria-expanded="false">
                            Tasks
                        </a>
                        <div className="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <Link to="/tasks/status/progress" className="dropdown-item">In Progress</Link>
                            <Link to="/tasks/status/frozen" className="dropdown-item">Frozen</Link>
                            <Link to="/tasks/status/potential" className="dropdown-item">Potential</Link>
                            <Link to="/tasks/status/cancelled" className="dropdown-item">Cancelled</Link>
                            <Link to="/tasks/status/completed" className="dropdown-item">Completed</Link>
                            <Link to="/tasks" className="dropdown-item">All</Link>
                        </div>
                    </li>
                    <li className="nav-item">
                        <Link to="/tasks/todo" className="nav-link">Todo</Link>
                    </li>
                    <li className="nav-item">
                        <Link to="/tasks/reminders" className="nav-link">Reminders</Link>
                    </li>
                    <li className="nav-item">
                        <a href="/logout" className="nav-link">Logout</a>
                    </li>
                </ul>
            </nav>
            <Switch>
                <Route path="/tasks/reminders">
                    <TasksPage url='/api/tasks/reminders' nested={false}/>
                </Route>
                <Route path="/tasks/todo">
                    <TasksPage url='/api/tasks/todo'/>
                </Route>
                <Route path="/tasks/status/progress">
                    <TasksPage url='/api/tasks/status/progress' nested={false}/>
                </Route>
                <Route path="/tasks/status/frozen">
                    <TasksPage url='/api/tasks/status/frozen'/>
                </Route>
                <Route path="/tasks/status/potential">
                    <TasksPage url='/api/tasks/status/potential'/>
                </Route>
                <Route path="/tasks/status/cancelled">
                    <TasksPage url='/api/tasks/status/cancelled'/>
                </Route>
                <Route path="/tasks/status/completed">
                    <TasksPage url='/api/tasks/status/completed'/>
                </Route>
                <Route path="/">
                    <TasksPage url='/api/tasks' />
                </Route>
            </Switch>
        </Router>
    );
}

export default App;
