
import React from 'react';
import {Link} from "react-router-dom";
import './PanelHeadingTask.scss';
import OpenIcon from "../../../App/OpenIcon";

const PanelHeadingTask = ({task, backLink}) => {
    return (
        <div className="panel-heading-task">
            { task.title ? <span className="panel-heading-task-title" title={task.title}>{task.title}</span> : null }
            { backLink ? <Link className="btn btn-default" to={backLink}><OpenIcon name="share-boxed"/></Link> : null }
        </div>
    );
}

export default PanelHeadingTask;
