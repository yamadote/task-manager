
import React from 'react';
import {Link} from "react-router-dom";
import './PanelHeadingTask.scss';

const PanelHeadingTask = ({task, backLink}) => {
    return (
        <div className="panel-heading-task">
            {task.title ? <span className="panel-heading-task-title">{task.title}</span> : null}
            <Link className="btn btn-default" to={backLink}>
                <span className="oi oi-share-boxed"/>
            </Link>
        </div>
    );
}

export default PanelHeadingTask;
