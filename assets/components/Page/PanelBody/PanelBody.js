
import React from 'react';

const PanelBody = ({children}) => {
    return (
        <div className="panel-body">
            <div className="content-row">
                <div className="row">
                    {children}
                </div>
            </div>
        </div>
    );
}

export default PanelBody;
