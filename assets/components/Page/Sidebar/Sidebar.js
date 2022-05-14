
import React from 'react';
import './Sidebar.scss';
import {Link} from "react-router-dom";
import Icon from "../../App/Icon";

const Sidebar = ({root, onSearch, reminderNumber}) => {
    const prefix = root === null ? '' : '/' + root.id;
    const searchInputKeyDown = (event) => {
        setTimeout(() => {
            onSearch(event.target.value)
        });
    }
    return (
        <div className="col-xs-6 col-sm-3 sidebar-offcanvas" role="navigation">
            <ul className="list-group">
                <li className="list-group-item">
                    <span><Icon name="align-justify"/> <b>SIDE PANEL</b></span>
                </li>
                <li className="list-group-item list-group-item-search">
                    <input type="text" onKeyDown={searchInputKeyDown} className="form-control search-query" placeholder="Search" />
                </li>
                <li className="list-group-item">
                    <Link to={prefix + "/tasks"}><Icon name="list-alt"/>All Tasks</Link>
                </li>
                <li className="list-group-item">
                    <Link to={prefix + "/tasks/reminders"}>
                        <Icon name="bell"/>Reminders
                        { reminderNumber ? <div className="badge badge-warning">{reminderNumber}</div> : null }
                    </Link>
                </li>
                <li className="list-group-item">
                    <Link to={prefix + "/tasks/status/progress"}><Icon name="flag"/>In Progress</Link>
                </li>
                <li className="list-group-item">
                    <Link to={prefix + "/tasks/todo"}><Icon name="flash"/>Todo</Link>
                </li>
                <li className="list-group-item">
                    <Link to={prefix + "/tasks/status/frozen"}><Icon name="certificate"/>Frozen</Link>
                </li>
                <li className="list-group-item">
                    <Link to={prefix + "/tasks/status/potential"}><Icon name="calendar"/>Potential</Link>
                </li>
                <li className="list-group-item">
                    <Link to={prefix + "/tasks/status/cancelled"}><Icon name="remove"/>Cancelled</Link>
                </li>
                <li className="list-group-item">
                    <Link to={prefix + "/tasks/status/completed"}><Icon name="ok"/>Completed</Link>
                </li>
            </ul>
        </div>
    );
}

export default Sidebar;
