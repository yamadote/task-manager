
import React from 'react';
import {Link} from "react-router-dom";
import Helper from "../../App/Helper";
import './TaskPanelHeading.scss';

const TaskPanelHeading = ({title, root, events}) => {
    return (
        <div className="panel-heading">
            <h3 className="panel-title">
                <button className="toggle-sidebar btn-to-link"><span className="fa fa-angle-double-left" data-toggle="offcanvas" title="Maximize Panel" /></button>
                {title}
                {/*{root?.title}*/}
                {/*<Link to={Helper.getTaskPageUrl(root?.parent)} className="nav-link">Back</Link>*/}
                <button className="btn btn-default" onClick={() => events.createNewTask(root?.id)}>
                    <span className="oi oi-plus"/>
                </button>
                <button className="btn btn-default" onClick={() => events.reload()}>
                    <span className="oi oi-reload"/>
                </button>
            </h3>
        </div>
    );
}

export default TaskPanelHeading;
