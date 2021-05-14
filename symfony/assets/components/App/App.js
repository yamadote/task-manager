
import React from 'react';
import './App.scss';

class App extends React.Component {
    constructor() {
        super();

        this.state = {
            entries: []
        };
    }

    componentDidMount() {
        // fetch('https://jsonplaceholder.typicode.com/posts/')
        //     .then(response => response.json())
        //     .then(entries => {
        //         this.setState({
        //             entries
        //         });
        //     });
    }

    render() {
        return (
            <div className="container">
                Test component
            </div>
        );
    }
}

export default App;
