
import React from 'react';
import {BrowserRouter as Router, Switch} from "react-router-dom";
import './App.scss';
import Config from "./../App/Config";
import TaskPageHandlers from "../TasksPage/TasksPage";
import Navbar from "../Navbar/Navbar";

const App = () => {
    const {renderTaskPage, events} = TaskPageHandlers()
    return (
        <Router>
            <Navbar events={events} />
            <Switch>
                {renderTaskPage("/tasks/reminders", Config.apiUrlPrefix + "/tasks/reminders", false)}
                {renderTaskPage("/tasks/todo", Config.apiUrlPrefix + "/tasks/todo")}
                {renderTaskPage("/tasks/status/progress", Config.apiUrlPrefix + "/tasks/status/progress", false)}
                {renderTaskPage("/tasks/status/frozen", Config.apiUrlPrefix + "/tasks/status/frozen")}
                {renderTaskPage("/tasks/status/potential", Config.apiUrlPrefix + "/tasks/status/potential")}
                {renderTaskPage("/tasks/status/cancelled", Config.apiUrlPrefix + "/tasks/status/cancelled")}
                {renderTaskPage("/tasks/status/completed", Config.apiUrlPrefix + "/tasks/status/completed")}
                {renderTaskPage("/", Config.apiUrlPrefix + "/tasks")}
            </Switch>
        </Router>
    );
}

export default App;
