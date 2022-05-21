
import React from 'react';
import Button from "../../../App/Button";
import './ActionTask.scss';

const ActionTask = ({task}) => {
    const writeToClipboard = () => navigator.clipboard.writeText(task.title);
    return (
        <div className="column-content" title={task.title}>
            <Button onClick={writeToClipboard}><span className="oi oi-clipboard"/></Button>
            <div className="title">{task.title}</div>
        </div>
    );
}

export default ActionTask;
