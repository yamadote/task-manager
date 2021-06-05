
import React from 'react';
import {Link} from "react-router-dom";

const TaskChildrenPageButton = ({task}) => {
    return (
        <Link to={'/' + task.id + '/tasks'} className="title-button btn btn-sm task-children-page-button">
            <span className="oi oi-align-center"/>
        </Link>
    );
}

export default TaskChildrenPageButton;
