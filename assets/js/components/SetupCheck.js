// ./assets/js/components/Users.js

import React, {Component} from 'react';
import axios from 'axios';

class SetupCheck extends Component {
    constructor() {
        super();
        this.state = { setupCheck: {}, loading: true};
    }

    componentDidMount() {
        this.checkApiSetup();
    }

    checkApiSetup() {
        axios.get(`http://telemedi-zadanie.localhost/api/setup-check?testParam=1`).then(response => {
            let responseIsOK = response.data && response.data.testParam === 1
            this.setState({ setupCheck: responseIsOK, loading: false})
        })
    }

    render() {
        const loading = this.state.loading;
        return(
            <div>
                <section className="row-section">
                    <div className="container">
                        <div className="row mt-5">
                            <div className="col-md-8 offset-md-2">
                                <h2 className="text-center"><span>This is a test</span> @ Telemedi</h2>

                        {loading ? (
                            <div className={'text-center'}>
                                <span className="fa fa-spin fa-spinner fa-4x"></span>
                            </div>
                        ) : (
                            <div className={'text-center'}>
                                { this.state.setupCheck === true ? (
                                    <h3 class={'text-success text-bold'}><strong>React app works!</strong></h3>
                                ) : (
                                    <h3 className={'text-error text-bold'}><strong>React app doesn't work :(</strong></h3>
                                )}
                            </div>
                        )}
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        )
    }
}
export default SetupCheck;