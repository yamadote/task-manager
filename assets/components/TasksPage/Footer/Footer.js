
import React from 'react';
import './Footer.scss';

const Footer = ({tasks}) => {
    if (!tasks || tasks.length === 0) {
        return null;
    }
    return (
        <div className='task-amount'>Tasks Amount: {tasks.length}</div>
    );
}

export default Footer;
