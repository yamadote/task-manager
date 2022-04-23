
import React from 'react';
import './PanelHeading.scss';

const PanelHeading = ({title}) => {
    return (
        <div className="panel-heading">
            <button className="toggle-sidebar btn-to-link">
                <span className="fa fa-angle-double-left" data-toggle="offcanvas" title="Maximize Panel" />
            </button>
            <h3 className="panel-title">
                {title}
            </h3>
        </div>
    );
}

export default PanelHeading;
