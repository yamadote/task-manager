
import React, {useLayoutEffect, useState} from 'react';
import Header from "../Header/Header";
import Sidebar from "../Sidebar/Sidebar";
import PanelHeading from "../PanelHeading/PanelHeading";
import Helper from "../App/Helper";
import Config from "../App/Config";

const TasksPage = () => {

    const [search, setSearch] = useState("");
    const [reminderNumber, setReminderNumber] = useState(undefined);

    const events = new function () {
        return {
            init: () => {
                Helper.fetchJson(Config.apiUrlPrefix + "/settings")
                    .then(response => setReminderNumber(response.reminderNumber));
            },
            onSearchUpdate: () => {
                console.log("TODO SEARCH: " + search);
            }
        }
    }

    useLayoutEffect(events.init, []);
    useLayoutEffect(events.onSearchUpdate, [search]);

    return (
        <div>
            <Header/>
            <div className="container-fluid main-container">
                <div className="row row-offcanvas row-offcanvas-left">
                    <Sidebar root={null} onSearch={setSearch} reminderNumber={reminderNumber}/>
                    <div className="col-xs-12 col-sm-9 content">
                        <div className="panel panel-default">
                            <PanelHeading title={<span><i className="glyphicon glyphicon-cog" />Settings</span>}/>
                            <div className="panel-body">
                                <div className="content-row">
                                    <div className="row">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default TasksPage;
