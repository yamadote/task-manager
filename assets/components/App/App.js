
import React from 'react';
import {BrowserRouter as Router, Route, Switch} from "react-router-dom";
import Config from "./../App/Config";
import TasksPage from "../TasksPage/TasksPage";
import SettingsPage from "../SettingsPage/SettingsPage";
import './App.scss';
import Icon from "./Icon";
import HistoryPage from "../HistoryPage/HistoryPage";

const App = () => {
    const renderTasksPage = (title, icon, url = "", nested = true) => {
        let fetchFrom = Config.apiUrlPrefix + "/tasks" + url;
        return <TasksPage title={title} icon={icon} fetchFrom={fetchFrom} nested={nested}/>
    }
    return (
        <Router>
            <Switch>
                <Route path="/:root?/tasks/reminders">
                    {renderTasksPage("Reminders", <Icon name="bell"/>, "/reminders", false)}
                </Route>
                <Route path="/:root?/tasks/todo">
                    {renderTasksPage("Todo", <Icon name="flash"/>, "/todo")}
                </Route>
                <Route path="/:root?/tasks/status/progress">
                    {renderTasksPage("In Progress", <Icon name="flag"/>, "/status/progress", false)}
                </Route>
                <Route path="/:root?/tasks/status/frozen">
                    {renderTasksPage("Frozen", <Icon name="certificate"/>, "/status/frozen")}
                </Route>
                <Route path="/:root?/tasks/status/potential">
                    {renderTasksPage("Potential", <Icon name="calendar"/>, "/status/potential")}
                </Route>
                <Route path="/:root?/tasks/status/cancelled">
                    {renderTasksPage("Cancelled", <Icon name="remove"/>, "/status/cancelled")}
                </Route>
                <Route path="/:root?/tasks/status/completed">
                    {renderTasksPage("Completed", <Icon name="ok"/>, "/status/completed")}
                </Route>
                <Route path="/:root?/tasks">
                    {renderTasksPage("All Tasks", <Icon name="list-alt"/>)}
                </Route>
                <Route path="/settings">
                    <SettingsPage/>
                </Route>
                <Route path="/history">
                    <HistoryPage/>
                </Route>
            </Switch>
        </Router>
    );
}

export default App;
