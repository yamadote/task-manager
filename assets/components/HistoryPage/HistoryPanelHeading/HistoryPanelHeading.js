
import React from 'react';
import PanelHeading from "../../Page/PanelHeading/PanelHeading";
import Button from "../../App/Button";
import PanelHeadingTask from "../../Page/PanelHeading/PanelHeadingTask/PanelHeadingTask";
import {useHistory} from "react-router-dom";
import OpenIcon from "../../App/OpenIcon";

const HistoryPanelHeading = ({title, icon, task, events}) => {
    const history = useHistory();
    return (
        <PanelHeading title={title} icon={icon}>
            { task ? <PanelHeadingTask task={task}/> : null }
            <div>
                <Button onClick={events.reload}><OpenIcon name="reload"/></Button>
                <Button onClick={history.goBack}><OpenIcon name="chevron-left"/></Button>
            </div>
        </PanelHeading>
    );
}

export default HistoryPanelHeading;
