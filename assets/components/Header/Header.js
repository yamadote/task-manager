
import React from 'react';
import './Header.scss';

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
                        <li className="dropdown">
                            <a data-toggle="dropdown" className="dropdown-toggle" href="#"><i className="glyphicon glyphicon-user" /> Account <b className="caret" /></a>
                            <ul role="menu" className="dropdown-menu">
                                <li><a href="#"><i className="glyphicon glyphicon-cog" /> Settings</a></li>
                                <li className="divider" />
                                <li><a href="/logout"><i className="glyphicon glyphicon-log-out" /> Signout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    );
}

export default Header;
