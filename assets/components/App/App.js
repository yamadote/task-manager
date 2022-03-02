
import React from 'react';
import {BrowserRouter as Router, Route, Switch} from "react-router-dom";
import Config from "./../App/Config";
import TasksPage from "../TasksPage/TasksPage";
import './App.scss';

const App = () => {
    return (
        <Router>
            <Switch>
                <Route path="/:root?/tasks/reminders">
                    <TasksPage fetchFrom={Config.apiUrlPrefix + "/tasks/reminders"} nested={false} title="Reminders"/>
                </Route>
                <Route path="/:root?/tasks/todo">
                    <TasksPage fetchFrom={Config.apiUrlPrefix + "/tasks/todo"} title="Todo"/>
                </Route>
                <Route path="/:root?/tasks/status/progress">
                    <TasksPage fetchFrom={Config.apiUrlPrefix + "/tasks/status/progress"} nested={false} title="In Progress"/>
                </Route>
                <Route path="/:root?/tasks/status/frozen">
                    <TasksPage fetchFrom={Config.apiUrlPrefix + "/tasks/status/frozen"} title="Frozen"/>
                </Route>
                <Route path="/:root?/tasks/status/potential">
                    <TasksPage fetchFrom={Config.apiUrlPrefix + "/tasks/status/potential"} title="Potential"/>
                </Route>
                <Route path="/:root?/tasks/status/cancelled">
                    <TasksPage fetchFrom={Config.apiUrlPrefix + "/tasks/status/cancelled"} title="Cancelled"/>
                </Route>
                <Route path="/:root?/tasks/status/completed">
                    <TasksPage fetchFrom={Config.apiUrlPrefix + "/tasks/status/completed"} title="Completed"/>
                </Route>
                <Route path="/:root?/tasks">
                    <TasksPage fetchFrom={Config.apiUrlPrefix + "/tasks"} title="All Tasks"/>
                </Route>
            </Switch>
        </Router>
    );
}

export default App;
