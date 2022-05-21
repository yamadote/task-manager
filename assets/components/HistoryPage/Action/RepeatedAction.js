
import React from 'react';

const RepeatedAction = ({action, events}) => {
    return (
        <tr className="repeated-action" >
            <td className="column clickable" colSpan={2} onClick={() => events.revealAction(action.id)} />
            <td className="column merged-column" />
        </tr>
    );
}

export default RepeatedAction;
