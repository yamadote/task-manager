
import React from 'react';

const ActionListSpacer = ({content}) => {
    return (
        <tr className="table-spacer">
            <td colSpan={3}>
                {content ? <div className="column-content">{content}</div> : null }
            </td>
        </tr>
    );
}

export default ActionListSpacer;
