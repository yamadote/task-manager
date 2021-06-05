
import React from 'react';
import {Link} from "react-router-dom";
import './NavbarRootPanel.scss';

const NavbarRootPanel = ({root, tasks}) => {
    const rootTitle = tasks?.find(task => task.id === root)?.title
    return (
        <div className="root-panel">
            <span className="root-title">
                { rootTitle }
            </span>
            <li className="nav-item">
                <Link to="/tasks" className="nav-link">Back</Link>
            </li>
        </div>
    )
}

export default NavbarRootPanel;
