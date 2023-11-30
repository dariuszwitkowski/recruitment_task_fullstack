import React, {Component} from 'react';
import {Route, Switch, Link} from 'react-router-dom';
import ExchangeRates from './ExchangeRates';

class Home extends Component {

  render() {
    return (
        <div>
          <nav className="navbar navbar-expand-lg navbar-dark bg-dark">
            <div id="navbarText">
              <ul className="navbar-nav mr-auto">
                <li className="nav-item">
                  <Link className={'nav-link'} to={'/exchange-rates'}>Kursy
                    walut</Link>
                </li>
              </ul>
            </div>
          </nav>
          <Switch>
            <Route path="/exchange-rates" component={ExchangeRates}/>
          </Switch>
        </div>
    );
  }
}

export default Home;
