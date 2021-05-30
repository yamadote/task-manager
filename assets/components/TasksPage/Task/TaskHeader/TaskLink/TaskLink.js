
import React from 'react';

const TaskLink = ({link}) => {
    return (
        <a href={link} target="_blank" className="title-link">
            <span className="oi oi-account-login"/>
        </a>
    )
}

export default TaskLink;
