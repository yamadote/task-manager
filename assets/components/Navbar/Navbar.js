
import React from 'react';
import {Link} from "react-router-dom";
import './Navbar.scss';

const Navbar = ({events}) => {
    return (
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
    );
}

export default Navbar;
