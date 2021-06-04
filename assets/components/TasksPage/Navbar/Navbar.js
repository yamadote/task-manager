
import React from 'react';
import {Link} from "react-router-dom";
import './Navbar.scss';

const Navbar = ({events, parent}) => {
    const prefix = parent === null ? '' : '/' + parent;
    return (
        <nav className="navbar justify-content-between">
            <ul className="nav nav-tabs">
                <li className="nav-item nav-item-all">
                    <Link to={prefix + "/tasks"} className="nav-link">All</Link>
                </li>
                <li className="nav-item nav-item-todo">
                    <Link to={prefix + "/tasks/todo"} className="nav-link">Todo</Link>
                </li>
                <li className="nav-item nav-item-reminders">
                    <Link to={prefix + "/tasks/reminders"} className="nav-link">Reminders</Link>
                </li>
                <li className="nav-item nav-item-progress">
                    <Link to={prefix + "/tasks/status/progress"} className="nav-link">In Progress</Link>
                </li>
                <li className="nav-item dropdown nav-item-tasks">
                    <a className="nav-link dropdown-toggle"
                       href="#" id="navbarDropdownMenuLink" data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">
                        Tasks
                    </a>
                    <div className="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <Link to={prefix + "/tasks/status/frozen"} className="dropdown-item">Frozen</Link>
                        <Link to={prefix + "/tasks/status/potential"} className="dropdown-item">Potential</Link>
                        <Link to={prefix + "/tasks/status/cancelled"} className="dropdown-item">Cancelled</Link>
                        <Link to={prefix + "/tasks/status/completed"} className="dropdown-item">Completed</Link>
                    </div>
                </li>
            </ul>
            <ul className="nav nav-tabs">
                { parent ? (
                    <li className="nav-item">
                        <Link to="/tasks" className="nav-link">Back</Link>
                    </li>
                ) : null }
                <li className="nav-item">
                    <button className="nav-link" onClick={() => events.reload()}>Reload</button>
                </li>
                <li className="nav-item nav-item-new-task">
                    <button className="nav-link" onClick={() => events.createNewTask(parent)}>New Task</button>
                </li>
                <li className="nav-item">
                    <a href="/logout" className="nav-link">Logout</a>
                </li>
            </ul>
        </nav>
    );
}

export default Navbar;
