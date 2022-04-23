
import React from 'react';
import {BrowserRouter as Router, Route, Switch} from "react-router-dom";
import Config from "./../App/Config";
import TasksPage from "../TasksPage/TasksPage";
import SettingsPage from "../SettingsPage/SettingsPage";
import './App.scss';

const App = () => {
    return (
        <Router>
            <Switch>
                <Route path="/:root?/tasks/reminders">
                    <TasksPage fetchFrom={Config.apiUrlPrefix + "/tasks/reminders"} nested={false}
                               title={<span><i className="glyphicon glyphicon-bell" />Reminders</span>}/>
                </Route>
                <Route path="/:root?/tasks/todo">
                    <TasksPage fetchFrom={Config.apiUrlPrefix + "/tasks/todo"}
                               title={<span><i className="glyphicon glyphicon-flash" />Todo</span>}/>
                </Route>
                <Route path="/:root?/tasks/status/progress">
                    <TasksPage fetchFrom={Config.apiUrlPrefix + "/tasks/status/progress"} nested={false}
                               title={<span><i className="glyphicon glyphicon-flag" />In Progress</span>}/>
                </Route>
                <Route path="/:root?/tasks/status/frozen">
                    <TasksPage fetchFrom={Config.apiUrlPrefix + "/tasks/status/frozen"}
                               title={<span><i className="glyphicon glyphicon-certificate" />Frozen</span>}/>
                </Route>
                <Route path="/:root?/tasks/status/potential">
                    <TasksPage fetchFrom={Config.apiUrlPrefix + "/tasks/status/potential"}
                               title={<span><i className="glyphicon glyphicon-calendar" />Potential</span>}/>
                </Route>
                <Route path="/:root?/tasks/status/cancelled">
                    <TasksPage fetchFrom={Config.apiUrlPrefix + "/tasks/status/cancelled"}
                               title={<span><i className="glyphicon glyphicon-remove" />Cancelled</span>}/>
                </Route>
                <Route path="/:root?/tasks/status/completed">
                    <TasksPage fetchFrom={Config.apiUrlPrefix + "/tasks/status/completed"}
                               title={<span><i className="glyphicon glyphicon-ok" />Completed</span>}/>
                </Route>
                <Route path="/:root?/tasks">
                    <TasksPage fetchFrom={Config.apiUrlPrefix + "/tasks"}
                               title={<span><i className="glyphicon glyphicon-list-alt" />All Tasks</span>}/>
                </Route>
                <Route path="/settings">
                    <SettingsPage/>
                </Route>
            </Switch>
        </Router>
    );
}

export default App;
