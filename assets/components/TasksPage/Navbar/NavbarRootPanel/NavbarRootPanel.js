
import React from 'react';
import {Link} from "react-router-dom";
import './NavbarRootPanel.scss';

const NavbarRootPanel = ({root}) => {
    return (
        <div className="root-panel">
            <span className="root-title">
                { root?.title }
            </span>
            <li className="nav-item">
                <Link to="/tasks" className="nav-link">Back</Link>
            </li>
        </div>
    )
}

export default NavbarRootPanel;
