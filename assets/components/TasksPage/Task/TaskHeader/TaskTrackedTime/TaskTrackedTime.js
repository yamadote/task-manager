
import React, {useEffect, useState} from 'react';
import './TaskTrackedTime.scss';

const TaskTrackedTime = ({task, activeTask}) => {
    const format = function (value) {
        return value < 10 ? '0' + value : value;
    }
    const isTrackingTime = activeTask && activeTask.path.includes(task.id);
    const [trackedTime, setTrackedTime] = useState(isTrackingTime ? activeTask.trackedTime : 0);
    const increaseTrackedTime = () => setTrackedTime(trackedTime + 1);

    useEffect(() => {
        if (isTrackingTime) {
            let myInterval = setInterval(increaseTrackedTime, 1000);
            return () => clearInterval(myInterval)
        }
    });

    const totalSeconds = task.trackedTime + task.childrenTrackedTime + trackedTime;
    if (totalSeconds === 0) {
        return null;
    }
    const hours = format(Math.floor(totalSeconds / 3600));
    const minutes = format(Math.floor((totalSeconds % 3600) / 60));
    const seconds = format(totalSeconds % 60);
    return (
        <span className="tracked-time">{ hours + ':' + minutes + ':' + seconds }</span>
    );
}

export default TaskTrackedTime;
