
import React from 'react';
import Helper from "../../App/Helper";
import PanelHeading from "../../Page/PanelHeading/PanelHeading";
import Button from "../../App/Button";
import PanelHeadingTask from "../../Page/PanelHeading/PanelHeadingTask/PanelHeadingTask";

const TaskPanelHeading = ({title, icon, root, events}) => {
    const renderPanelHeadingTask = (root) => {
        if (root) {
            const backLink = Helper.getTaskPageUrl(root?.parent);
            return <PanelHeadingTask task={root} backLink={backLink} />;
        }
    }
    return (
        <PanelHeading title={title} icon={icon}>
            { renderPanelHeadingTask(root) }
            <div>
                <Button onClick={() => events.toggleCalendar()}><span className="oi oi-credit-card"/></Button>
                <Button onClick={() => events.reload()}><span className="oi oi-reload"/></Button>
                <Button onClick={() => events.createNewTask(root?.id)}><span className="oi oi-plus"/></Button>
            </div>
        </PanelHeading>
    );
}

export default TaskPanelHeading;
