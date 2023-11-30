import React, {Component} from 'react';

class Spinner extends Component {
  render() {
    return (
        <div className={'text-center'}>
          <span className="fa fa-spin fa-spinner fa-4x"/>
        </div>
    );
  }
}

export default Spinner;
