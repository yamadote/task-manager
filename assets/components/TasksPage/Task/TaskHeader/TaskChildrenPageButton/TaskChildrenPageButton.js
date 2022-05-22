
import React from 'react';
import {Link} from "react-router-dom";
import Helper from "../../../../App/Helper";
import OpenIcon from "../../../../App/OpenIcon";

const TaskChildrenPageButton = ({task}) => {
    return (
        <Link to={Helper.getTaskPageUrl(task.id)} className="title-button">
            <OpenIcon name="align-center"/>
        </Link>
    );
}

export default TaskChildrenPageButton;
