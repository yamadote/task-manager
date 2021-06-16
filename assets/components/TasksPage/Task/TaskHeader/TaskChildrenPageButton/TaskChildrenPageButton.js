
import React from 'react';
import {Link} from "react-router-dom";
import Helper from "../../../../App/Helper";

const TaskChildrenPageButton = ({task}) => {
    return (
        <Link to={Helper.getTaskPageUrl(task.id)} className="title-button btn btn-sm task-children-page-button">
            <span className="oi oi-align-center"/>
        </Link>
    );
}

export default TaskChildrenPageButton;
