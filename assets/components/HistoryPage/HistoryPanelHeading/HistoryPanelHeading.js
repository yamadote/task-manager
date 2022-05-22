
import React from 'react';
import PanelHeading from "../../Page/PanelHeading/PanelHeading";
import Button from "../../App/Button";
import PanelHeadingTask from "../../Page/PanelHeading/PanelHeadingTask/PanelHeadingTask";
import {useHistory} from "react-router-dom";
import Helper from "../../App/Helper";
import OpenIcon from "../../App/OpenIcon";

const HistoryPanelHeading = ({title, icon, task, events}) => {
    const history = useHistory();
    const renderPanelHeadingTask = (task) => {
        if (task) {
            const backLink = Helper.getHistoryPageUrl();
            return <PanelHeadingTask task={task} backLink={backLink} />;
        }
    }
    return (
        <PanelHeading title={title} icon={icon}>
            { renderPanelHeadingTask(task) }
            <div>
                <Button onClick={events.reload}><OpenIcon name="reload"/></Button>
                <Button onClick={history.goBack}><OpenIcon name="chevron-left"/></Button>
            </div>
        </PanelHeading>
    );
}

export default HistoryPanelHeading;
