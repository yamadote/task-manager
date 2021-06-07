
import React from 'react';
import {Link, NavLink} from "react-router-dom";
import './Navbar.scss';
import NavbarRootPanel from "./NavbarRootPanel/NavbarRootPanel";

const Navbar = ({events, root}) => {
    const prefix = root === null ? '' : '/' + root.id;
    return (
        <nav className="navbar justify-content-between">
            <ul className="nav nav-tabs">
                <li className="nav-item nav-item-all">
                    <Link to={prefix + "/tasks"} className="nav-link">All</Link>
                </li>
                <li className="nav-item nav-item-todo">
                    <NavLink to={prefix + "/tasks/todo"}
                             className="nav-link"
                             activeClassName="nav-link active-tab">Todo</NavLink>
                </li>
                <li className="nav-item nav-item-reminders">
                    <NavLink to={prefix + "/tasks/reminders"}
                             className="nav-link"
                             activeClassName="nav-link active-tab">Reminders</NavLink>
                </li>
                <li className="nav-item nav-item-progress">
                    <NavLink to={prefix + "/tasks/status/progress"}
                             className="nav-link"
                             activeClassName="nav-link active-tab">In Progress</NavLink>
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
                { root ? <NavbarRootPanel root={root}/> : null }
                <li className="nav-item">
                    <button className="nav-link" onClick={() => events.reload()}>Reload</button>
                </li>
                <li className="nav-item nav-item-new-task">
                    <button className="nav-link" onClick={() => events.createNewTask(root?.id)}>New Task</button>
                </li>
                <li className="nav-item">
                    <a href="/logout" className="nav-link">Logout</a>
                </li>
            </ul>
        </nav>
    );
}

export default Navbar;
