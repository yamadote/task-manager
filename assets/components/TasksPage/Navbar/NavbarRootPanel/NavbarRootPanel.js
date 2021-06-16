
import React from 'react';
import {Link} from "react-router-dom";
import './NavbarRootPanel.scss';
import Helper from "../../../App/Helper";

const NavbarRootPanel = ({root}) => {
    return (
        <div className="root-panel">
            <span className="root-title">
                { root?.title }
            </span>
            <li className="nav-item">
                <Link to={Helper.getTaskPageUrl(root?.parent)} className="nav-link">Back</Link>
            </li>
        </div>
    )
}

export default NavbarRootPanel;
