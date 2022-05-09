
import React from 'react';
import {Link} from "react-router-dom";
import Helper from "../../App/Helper";
import '../../Page/PanelHeading/PanelHeading.scss';
import './TaskPanelHeading.scss';
import PanelHeading from "../../Page/PanelHeading/PanelHeading";

const TaskPanelHeading = ({title, icon, root, events}) => {
    return (
        <PanelHeading title={title} icon={icon}>
            <div className="panel-task-root">
                {root ? <span className="root-title">{root.title}</span> : ''}
                {root ? <Link className="btn btn-default" to={Helper.getTaskPageUrl(root?.parent)}><span className="oi oi-share-boxed"/></Link> : null}
            </div>
            <div>
                <button className="btn btn-default" onClick={() => events.toggleCalendar()}>
                    <span className="oi oi-credit-card"/>
                </button>
                <button className="btn btn-default" onClick={() => events.reload()}>
                    <span className="oi oi-reload"/>
                </button>
                <button className="btn btn-default" onClick={() => events.createNewTask(root?.id)}>
                    <span className="oi oi-plus"/>
                </button>
            </div>
        </PanelHeading>
    );
}

export default TaskPanelHeading;
