import React from 'react';
import CurrencyService from '../services/CurrencyService';
import '../../css/ExchangeRates.css';
import {withRouter} from 'react-router-dom';
import ExchangeRatesTable from '../components/ExchangeRatesTable';
import Spinner from '../components/Spinner';
import DatePicker from '../components/DatePicker';

class ExchangeRates extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      currencies: [],
      selectedDate: this.formatDate(new Date()),
      loading: true,
    };

    this.currencyService = new CurrencyService();
    this.handleDateChange = this.handleDateChange.bind(this);
  }

  modifyUrl(newDate) {
    const {history, location} = this.props;
    const searchParams = new URLSearchParams(location.search);
    searchParams.set('date', newDate);
    history.push(`${location.pathname}?${searchParams.toString()}`);
  };

  formatDate(date) {
    return date.toISOString().split('T')[0];
  }

  async componentDidMount() {
    const params = new URLSearchParams(window.location.search);
    const date = params.get('date');
    if (date) {
      await this.setState({selectedDate: date});
    }
    this.fetchCurrencyData();
  }

  async fetchCurrencyData() {
    this.setState({loading: true});
    try {
      const currenciesToday = await this.currencyService.fetchCurrencies(
          new Date().toISOString().split('T')[0]);
      const currenciesSelectedDate = await this.currencyService.fetchCurrencies(
          this.state.selectedDate);
      const currenciesData = this.parseData(currenciesToday['currencies'],
          currenciesToday['rates'], currenciesSelectedDate['rates']);
      this.setState({currencies: currenciesData, loading: false});
    }
    catch (error) {
      this.setState({loading: false});
      console.error(error);
    }
  }

  parseData(
      availableCurrencies, currenciesToday = [], currenciesSelectedDate = []) {
    const noData = 'N/A';
    return Object.keys(availableCurrencies).map(currency => {
      const currencyToday = currenciesToday.find(c => c.code === currency) ||
          {};
      const currencySelectedDate = currenciesSelectedDate.find(
          c => c.code === currency) || {};
      return {
        code: currency,
        currency: availableCurrencies[currency],
        buyToday: currencyToday.buy || noData,
        sellToday: currencyToday.sell || noData,
        buySelected: currencySelectedDate.buy || noData,
        sellSelected: currencySelectedDate.sell || noData,
      };
    });
  }

  handleDateChange(event) {
    if (!event.target.value || this.state.loading) {
      return;
    }
    this.setState({selectedDate: event.target.value}, () => {
      this.modifyUrl(this.state.selectedDate);
      this.fetchCurrencyData();
    });
  }

  render() {
    const {loading, currencies, selectedDate, warning} = this.state;

    return (
        <div>
          <DatePicker disabled={loading} selectedDate={selectedDate}
                      onDateChange={this.handleDateChange}/>
          {loading ? (<Spinner/>) : (<ExchangeRatesTable currencies={currencies}
                                                         selectedDate={selectedDate}/>)}
          <p>{warning}</p>
        </div>
    );
  }
}

export default withRouter(ExchangeRates);