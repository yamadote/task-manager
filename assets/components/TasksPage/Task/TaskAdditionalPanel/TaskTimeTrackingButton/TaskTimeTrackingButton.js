
import React from 'react';
import Button from "../../../../App/Button";

const TaskTimeTrackingButton = ({task, isActive, events}) => {
    return isActive
        ? <Button onClick={() => events.finishTask(task.id)} buttonStyle='info' buttonSize='sm'>Finish</Button>
        : <Button onClick={() => events.startTask(task.id)} buttonStyle='primary' buttonSize='sm'>Start</Button>
    ;
}

export default TaskTimeTrackingButton;
