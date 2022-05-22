
import React from 'react';
import Button from "../../../App/Button";
import './ActionTask.scss';
import {Link} from "react-router-dom";
import Helper from "../../../App/Helper";
import OpenIcon from "../../../App/OpenIcon";

const ActionTask = ({task}) => {
    const writeToClipboard = () => navigator.clipboard.writeText(task.title);
    return (
        <div className="column-content" title={task.title}>
            <Button onClick={writeToClipboard}><OpenIcon name="clipboard"/></Button>
            <Link to={Helper.getHistoryPageUrl(task)}>
                <Button><OpenIcon name="align-center"/></Button>
            </Link>
            <div className="title">{task.title}</div>
        </div>
    );
}

export default ActionTask;
