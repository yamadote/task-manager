
import React from 'react';
import OpenIcon from "../../../../App/OpenIcon";

const TaskLink = ({link}) => {
    if (link.includes("https://github.com/")) {
        return null;
    }
    return (
        <a href={link} target="_blank" className="title-link">
            <OpenIcon name="external-link"/>
        </a>
    )
}

export default TaskLink;
