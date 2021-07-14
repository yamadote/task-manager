
import React, {useEffect, useState} from 'react';
import './TaskTrackedTime.scss';

const TaskTrackedTime = ({task}) => {
    let format = function (value) {
        return value < 10 ? '0' + value : value;
    }
    let totalSeconds = task.trackedTime + task.childrenTrackedTime;
    if (totalSeconds === 0) {
        return null;
    }
    let hours = format(Math.floor(totalSeconds / 3600));
    let minutes = format(Math.floor((totalSeconds % 3600) / 60));
    let seconds = format(totalSeconds % 60);
    return (
        <span className="tracked-time">{ hours + ':' + minutes + ':' + seconds }</span>
    );
}

export default TaskTrackedTime;
