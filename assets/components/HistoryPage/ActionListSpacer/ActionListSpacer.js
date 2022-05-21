
import React from 'react';
import './ActionListSpacer.scss';

const ActionListSpacer = ({content}) => {
    return (
        <tr className="action-list-spacer">
            <td colSpan={3}>
                {content ? <div className="column-content">{content}</div> : null }
            </td>
        </tr>
    );
}

export default ActionListSpacer;
