
import React from 'react';

const TaskGithubLink = ({link}) => {
    if (!link.includes("https://github.com/")) {
        return null;
    }
    const issue = link.split('/').slice(-1)[0];
    return <a href={link} target="_blank" className="title-github-link">#{issue}</a>
}

export default TaskGithubLink;
