
import React from 'react';
import {BrowserRouter as Router, Switch} from "react-router-dom";
import Config from "./../App/Config";
import TaskPageHandlers from "../TasksPage/TasksPage";
import Navbar from "../Navbar/Navbar";
import './App.scss';

const App = () => {
    const {renderTaskPage, events} = TaskPageHandlers()
    const prefix = Config.apiUrlPrefix;
    return (
        <Router>
            <Navbar events={events.createNewTask} />
            <Switch>
                {renderTaskPage("/tasks/reminders", prefix + "/tasks/reminders", false)}
                {renderTaskPage("/tasks/todo", prefix + "/tasks/todo")}
                {renderTaskPage("/tasks/status/progress", prefix + "/tasks/status/progress", false)}
                {renderTaskPage("/tasks/status/frozen", prefix + "/tasks/status/frozen")}
                {renderTaskPage("/tasks/status/potential", prefix + "/tasks/status/potential")}
                {renderTaskPage("/tasks/status/cancelled", prefix + "/tasks/status/cancelled")}
                {renderTaskPage("/tasks/status/completed", prefix + "/tasks/status/completed")}
                {renderTaskPage("/", prefix + "/tasks")}
            </Switch>
        </Router>
    );
}

export default App;
