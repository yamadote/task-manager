
import React, {useState} from 'react';
import './TaskTitle.scss';

const TaskTitle = ({task, children, events}) => {
    const [isTitleChanging, setTitleChanging] = useState(false);
    const titleClassName = "title" + (isTitleChanging ? " changing" : "");

    const onTitleChange = event => events.updateTaskTitle(task.id, event.target.value, setTitleChanging);
    const onAdditionalPanelViewButton = () => events.updateTaskAdditionalPanelViewSetting(task.id)

    const displayChildrenViewButton = () => {
        const onChildrenViewButton = () => events.updateTaskChildrenViewSetting(task.id);
        return <button onClick={onChildrenViewButton} className='btn btn-sm'>></button>;
    }
    return (
        <div className="title-group">
            { children.length > 0 ? displayChildrenViewButton() : null }
            <input className={titleClassName} type="text" value={task.title} onChange={onTitleChange} />
            {!task.link ? null : <a href={task.link} target="_blank" className="float-right mt-2">link</a>}
            <button onClick={onAdditionalPanelViewButton} className='btn btn-sm'>^</button>
        </div>
    )
}

export default TaskTitle;
