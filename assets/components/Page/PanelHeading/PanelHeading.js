
import React from 'react';
import './PanelHeading.scss';

const PanelHeading = ({children, title, icon}) => {
    return (
        <div className="panel-heading">
            <button className="toggle-sidebar btn-to-link">
                <span className="fa fa-angle-double-left" data-toggle="offcanvas" title="Maximize Panel" />
            </button>
            <h3 className="panel-title">
                <span>{icon}{title}</span>
            </h3>
            {children}
        </div>
    );
}

export default PanelHeading;
