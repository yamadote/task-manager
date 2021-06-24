
import React from 'react';
import TasksAmount from "./TasksAmount/TasksAmount";

const Footer = ({tasks, root, nested}) => {
    return (
        <TasksAmount tasks={tasks} root={root} nested={nested}/>
    );
}

export default Footer;
