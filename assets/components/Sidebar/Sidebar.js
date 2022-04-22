
import React from 'react';
import './Sidebar.scss';
import {Link} from "react-router-dom";

const Sidebar = ({root, search, reminderNumber}) => {
    const prefix = root === null ? '' : '/' + root.id;
    const searchInputKeyDown = (event) => {
        setTimeout(function() {
            search(event.target.value)
        });
    }
    return (
        <div className="col-xs-6 col-sm-3 sidebar-offcanvas" role="navigation">
            <ul className="list-group">
                <li className="list-group-item">
                    <span><i className="glyphicon glyphicon-align-justify" /> <b>SIDE PANEL</b></span>
                </li>
                <li className="list-group-item list-group-item-search">
                    <input type="text" onKeyDown={searchInputKeyDown} className="form-control search-query" placeholder="Search" />
                </li>
                <li className="list-group-item">
                    <Link to={prefix + "/tasks"}><i className="glyphicon glyphicon-list-alt" />All Tasks</Link>
                </li>
                <li className="list-group-item">
                    <Link to={prefix + "/tasks/reminders"}>
                        <i className="glyphicon glyphicon-bell" />Reminders
                        { reminderNumber ? <div className="badge badge-warning">{reminderNumber}</div> : null }
                    </Link>
                </li>
                <li className="list-group-item">
                    <Link to={prefix + "/tasks/status/progress"}><i className="glyphicon glyphicon-flag" />In Progress</Link>
                </li>
                <li className="list-group-item">
                    <Link to={prefix + "/tasks/todo"}><i className="glyphicon glyphicon-flash" />Todo</Link>
                </li>
                <li className="list-group-item">
                    <Link to={prefix + "/tasks/status/frozen"}><i className="glyphicon glyphicon-certificate" />Frozen</Link>
                </li>
                <li className="list-group-item">
                    <Link to={prefix + "/tasks/status/potential"}><i className="glyphicon glyphicon-calendar" />Potential</Link>
                </li>
                <li className="list-group-item">
                    <Link to={prefix + "/tasks/status/cancelled"}><i className="glyphicon glyphicon-remove" />Cancelled</Link>
                </li>
                <li className="list-group-item">
                    <Link to={prefix + "/tasks/status/completed"}><i className="glyphicon glyphicon-ok" />Completed</Link>
                </li>
            </ul>
        </div>
    );
}

export default Sidebar;
