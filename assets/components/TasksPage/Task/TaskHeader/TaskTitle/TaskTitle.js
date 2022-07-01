
import React, {useState} from 'react';

const TaskTitle = ({task, events}) => {
    const [isTitleChanging, setTitleChanging] = useState(false);
    const className = "title mr-1 " + (isTitleChanging ? " changing" : "");
    const onChange = event => events.updateTaskTitle(task.id, event.target.value, setTitleChanging);
    return <input autoFocus={task.autoFocus} className={className} type="text" value={task.title} onChange={onChange} />
}

export default TaskTitle;
