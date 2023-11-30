import React, {Component} from 'react';

class ExchangeRatesTable extends Component {
  render() {
    return (
        <table className="currency-table">
          <thead>
          <tr>
            <th>Waluta</th>
            <th>Kod</th>
            <th>Kurs Kupna ({this.props.selectedDate})</th>
            <th>Kurs Sprzedaży ({this.props.selectedDate})</th>
            <th>Kurs Kupna (Dzisiaj)</th>
            <th>Kurs Sprzedaży (Dzisiaj)</th>
          </tr>
          </thead>
          <tbody>
          {this.props.currencies.map(currency => (
              <tr key={currency.code}>
                <td>{currency.currency}</td>
                <td>{currency.code}</td>
                <td>{currency.buySelected}</td>
                <td>{currency.sellSelected}</td>
                <td>{currency.buyToday}</td>
                <td>{currency.sellToday}</td>
              </tr>
          ))}
          </tbody>
        </table>
    );
  }
}

export default ExchangeRatesTable;
