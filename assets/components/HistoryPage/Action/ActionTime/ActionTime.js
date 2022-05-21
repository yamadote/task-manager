
import React from 'react';
import moment from "moment";
import './ActionTime.scss';

const Action = ({timestamp}) => {
    const time = moment.unix(timestamp).format('HH:mm');
    return <div className="column-content">{time}</div>;
}

export default Action;
