
import React, {useState} from 'react';
import "./Search.scss";

const Search = ({onSearch}) => {
    const [value, setValue] = useState("");
    const onChange = (e) => {
        const value = e.target.value;
        setValue(value);
        onSearch(value)
    }
    let className = "form-control search-query";
    if (value) {
        className += " searching";
    }
    return (
        <input type="text" onChange={onChange} className={className} placeholder="Search" />
    );
}

export default Search;
