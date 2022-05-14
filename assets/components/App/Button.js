
import React from 'react';

const Button = ({onClick, className, buttonStyle, buttonSize, onFocus, children}) => {
    buttonStyle = buttonStyle ?? 'default';
    const prepareClassName = () => {
        let preparedClassName = ' btn btn-' + buttonStyle + ' ';
        if (buttonSize) {
            preparedClassName += ' btn-' + buttonSize + ' ';
        }
        if (className) {
            preparedClassName += ' ' + className;
        }
        return preparedClassName;
    }
    const wrappedOnFocus = (e) => {
        if (onFocus) {
            onFocus(e);
        }
        e.target.blur();
    }
    return (
        <button onClick={onClick} className={prepareClassName()} onFocus={wrappedOnFocus}>
            {children}
        </button>
    );
}

export default Button;
