
import React from 'react';
import Helper from "../../../../App/Helper";

const TaskGithubLink = ({link}) => {
    if (!Helper.isGithubLink(link)) {
        return null;
    }
    const issue = Helper.getGithubIssueNumber(link)
    return <a href={link} target="_blank" className="title-github-link">{issue}</a>
}

export default TaskGithubLink;
