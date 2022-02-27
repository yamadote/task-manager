
import React from 'react';

const TaskTimeTrackingButton = ({task, isActive, events}) => {
    return isActive
        ? <button onClick={() => events.finishTask(task.id)} className='btn btn-sm btn-info'>Finish</button>
        : <button onClick={() => events.startTask(task.id)} className='btn btn-sm btn-primary'>Start</button>
    ;
}

export default TaskTimeTrackingButton;
