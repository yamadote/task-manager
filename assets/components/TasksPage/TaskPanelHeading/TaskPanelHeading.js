
import React from 'react';
import Helper from "../../App/Helper";
import PanelHeading from "../../Page/PanelHeading/PanelHeading";
import Button from "../../App/Button";
import PanelHeadingTask from "../../Page/PanelHeading/PanelHeadingTask/PanelHeadingTask";
import OpenIcon from "../../App/OpenIcon";

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
                <Button onClick={() => events.toggleCalendar()}><OpenIcon name="credit-card"/></Button>
                <Button onClick={() => events.reload()}><OpenIcon name="reload"/></Button>
                <Button onClick={() => events.createNewTask(root?.id)}><OpenIcon name="plus"/></Button>
            </div>
        </PanelHeading>
    );
}

export default TaskPanelHeading;
