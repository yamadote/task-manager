
import React from 'react';
import Action from "../Action/Action";

const ActionList = ({actions}) => {
    if (!actions) {
        return null;
    }
    return (
        <div className="table-responsive">
        <table className="table table-bordered history-action-list">
            <thead>
                <tr>
                    <td className="column time-column">Time</td>
                    <td className="column message-column">Action</td>
                    <td className="column task-column">Task</td>
                </tr>
            </thead>
            <tbody>
                {actions.filter(action => !action.isHidden).map(action => {
                    return <Action key={action.id} action={action} />
                })}
            </tbody>
        </table>
        </div>
    );
}

export default ActionList;
