
import React from 'react';
import {Link} from "react-router-dom";
import Helper from "../../App/Helper";
import './TaskPanelHeading.scss';

const TaskPanelHeading = ({title, root, events}) => {
    return (
        <div className="panel-heading">
            <button className="toggle-sidebar btn-to-link">
                <span className="fa fa-angle-double-left" data-toggle="offcanvas" title="Maximize Panel" />
            </button>
            <h3 className="panel-title">
                {title}
            </h3>
            <div className="panel-task-root">
                {root ? <span className="root-title">{root.title}</span> : ''}
                {root ? <Link className="btn btn-default" to={Helper.getTaskPageUrl(root?.parent)}><span className="oi oi-share-boxed"/></Link> : null}
            </div>
            <div>
                <button className="btn btn-default" onClick={() => events.reload()}>
                    <span className="oi oi-reload"/>
                </button>
                <button className="btn btn-default" onClick={() => events.createNewTask(root?.id)}>
                    <span className="oi oi-plus"/>
                </button>
            </div>
        </div>
    );
}

export default TaskPanelHeading;
