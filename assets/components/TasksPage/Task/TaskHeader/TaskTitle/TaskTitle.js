
import React, {useState} from 'react';

const TaskTitle = ({task, events}) => {
    const [isTitleChanging, setTitleChanging] = useState(false);
    const titleClassName = "title mr-1 " + (isTitleChanging ? " changing" : "");
    const onTitleChange = event => events.updateTaskTitle(task.id, event.target.value, setTitleChanging);
    return <input className={titleClassName} type="text" value={task.title} onChange={onTitleChange} />
}

export default TaskTitle;
