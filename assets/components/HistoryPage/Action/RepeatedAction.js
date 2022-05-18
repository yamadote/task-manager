
import React from 'react';

const RepeatedAction = ({action, events}) => {
    return (
        <tr onClick={() => events.revealAction(action.id)} className="repeated-action" >
            <td colSpan={3}/>
        </tr>
    );
}

export default RepeatedAction;
