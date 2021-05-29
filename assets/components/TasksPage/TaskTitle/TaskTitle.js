
import React, {useState} from 'react';
import './TaskTitle.scss';

const TaskTitle = (props) => {
    const task = props.task;
    const [isTitleChanging, setTitleChanging] = useState(false);
    const titleClassName = "title" + (isTitleChanging ? " changing" : "");

    const onTitleChange = event => props.events.updateTaskTitle(task.id, event.target.value, setTitleChanging);
    const onAdditionalPanelViewButton = () => props.events.updateTaskAdditionalPanelViewSetting(task.id)

    const displayChildrenViewButton = () => {
        const onChildrenViewButton = () => props.events.updateTaskChildrenViewSetting(task.id);
        return (<button onClick={onChildrenViewButton} className='btn btn-sm'>></button>);
    }
    return (
        <div className="title-group">
            { props.children.length > 0 ? displayChildrenViewButton() : null }
            <input className={titleClassName} type="text" value={task.title} onChange={onTitleChange} />
            {!task.link ? null : <a href={task.link} target="_blank" className="float-right mt-2">link</a>}
            <button onClick={onAdditionalPanelViewButton} className='btn btn-sm'>^</button>
        </div>
    )
}

export default TaskTitle;
