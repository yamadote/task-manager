
import React from 'react';
import {BrowserRouter as Router, Route, Switch} from "react-router-dom";
import Config from "./../App/Config";
import TasksPage from "../TasksPage/TasksPage";
import './App.scss';

const App = () => {
    return (
        <Router>
            <Switch>
                <Route path="/:parent?/tasks/reminders">
                    <TasksPage fetchFrom={Config.apiUrlPrefix + "/tasks/reminders"} nested={false}/>
                </Route>
                <Route path="/:parent?/tasks/todo">
                    <TasksPage fetchFrom={Config.apiUrlPrefix + "/tasks/todo"}/>
                </Route>
                <Route path="/:parent?/tasks/status/progress">
                    <TasksPage fetchFrom={Config.apiUrlPrefix + "/tasks/status/progress"} nested={false}/>
                </Route>
                <Route path="/:parent?/tasks/status/frozen">
                    <TasksPage fetchFrom={Config.apiUrlPrefix + "/tasks/status/frozen"}/>
                </Route>
                <Route path="/:parent?/tasks/status/potential">
                    <TasksPage fetchFrom={Config.apiUrlPrefix + "/tasks/status/potential"}/>
                </Route>
                <Route path="/:parent?/tasks/status/cancelled">
                    <TasksPage fetchFrom={Config.apiUrlPrefix + "/tasks/status/cancelled"}/>
                </Route>
                <Route path="/:parent?/tasks/status/completed">
                    <TasksPage fetchFrom={Config.apiUrlPrefix + "/tasks/status/completed"}/>
                </Route>
                <Route path="/:parent?/">
                    <TasksPage fetchFrom={Config.apiUrlPrefix + "/tasks"}/>
                </Route>
            </Switch>
        </Router>
    );
}

export default App;
