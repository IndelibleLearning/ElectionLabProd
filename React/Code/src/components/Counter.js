import React from 'react';
import { connect } from 'react-redux';

function Counter({ counter, dispatch }) {
    return (
        <div>
            <p>{counter}</p>
            <button onClick={() => dispatch({ type: 'INCREMENT' })}>Increment</button>
            <button onClick={() => dispatch({ type: 'DECREMENT' })}>Decrement</button>
        </div>
    );
}

const mapStateToProps = state => ({
    counter: state.counter
});

export default connect(mapStateToProps)(Counter);
