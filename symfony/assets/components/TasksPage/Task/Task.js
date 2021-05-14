
import React from 'react';

const Task = (props) => {
    return (
        <div className="task">
            <div>Title: {props.title}</div>
        </div>
    );
}

export default Task;
