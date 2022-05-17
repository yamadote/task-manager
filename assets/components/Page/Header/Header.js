
import React from 'react';
import './Header.scss';
import {Link} from "react-router-dom";
import Icon from "../../App/Icon";

const Header = () => {
    return (
        <nav role="navigation" className="navbar navbar-custom">
            <div className="container-fluid">
                <div className="navbar-header">
                    <button data-target="#bs-content-row-navbar-collapse-5" data-toggle="collapse" className="navbar-toggle" type="button">
                        <span className="sr-only">Toggle navigation</span>
                        <span className="icon-bar" />
                        <span className="icon-bar" />
                        <span className="icon-bar" />
                    </button>
                    <a href="#" className="navbar-brand">
                        <i className="fa fa-check-square-o" style={{color: '#48CFAD', marginRight: '5px'}} />
                        <div style={{display: 'inline-block'}}>Task Manager</div>
                    </a>
                </div>
                <div id="bs-content-row-navbar-collapse-5" className="collapse navbar-collapse">
                    <ul className="nav navbar-nav navbar-right">
                        <li className="active"><a href="#">Documentation</a></li>
                        <li className="active"><Link to="/history">History</Link></li>
                        <li className="dropdown">
                            <a data-toggle="dropdown" className="dropdown-toggle" href="#">
                                <Icon name="user" /> Account <b className="caret" />
                            </a>
                            <ul role="menu" className="dropdown-menu">
                                <li><Link to="/settings"><Icon name="cog" /> Settings</Link></li>
                                <li className="divider" />
                                <li><a href="/logout"><Icon name="log-out" /> Signout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    );
}

export default Header;
