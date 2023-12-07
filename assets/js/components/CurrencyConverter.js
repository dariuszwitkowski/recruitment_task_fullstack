import React, { Component } from 'react'
import '../../css/CurrencyConverter.css'

class CurrencyConverter extends Component {
  constructor (props) {
    super(props)

    const currenciesTo = Object.keys(this.props.currenciesToday.currencies)
    const currenciesFrom = this.filterSellableCurrencies()

    this.state = {
      currencyOptionsFrom: currenciesFrom,
      currencyOptionsTo: currenciesTo,
      calcResult: 0,
      currencyFrom: currenciesFrom[0],
      currencyTo: currenciesTo[0],
      amount: 0,
      rates: this.props.currenciesToday.rates
    }
  }

  async handleDataChange (stateChange) {
    await this.setState(stateChange)
    this.convertSelectedCurrency()
  }

  filterSellableCurrencies () {
    const currenciesFrom = []
    Object.keys(this.props.currenciesToday.currencies)
      .forEach((c) => {
        if (this.findRate(c, 'buy', this.props.currenciesToday.rates)) {
          currenciesFrom.push(c)
        }
      })

    return currenciesFrom
  }

  convertSelectedCurrency () {
    const rateFrom = this.findRate(this.state.currencyFrom, 'buy', this.state.rates)
    const rateTo = this.findRate(this.state.currencyTo, 'sell', this.state.rates)
    const amount = this.state.amount
    if (rateTo && rateFrom && amount >= 0) {
      let finalPrice = amount * rateFrom / rateTo
      this.setState({ calcResult: finalPrice.toFixed(2) })
    } else {
      this.setState({ calcResult: 0 })
    }
  }

  findRate (code, transactionType, rates) {
    let rate = 0
    rates.forEach((e) => {
      if (e.code === code) {
        if (transactionType === 'sell') {
          rate = e.sell
        } else if (transactionType === 'buy') {
          rate = e.buy
        } else {
          console.error('Incorrect transaction type')
        }
      }
    })

    return rate
  }

  render () {
    const { currencyOptionsFrom, currencyOptionsTo, calcResult } = this.state
    return (
      <div className="currency-converter">
        <div className="currency-converter-item">Kowerter walut: </div>
        <input className="currency-converter-item" type="number"
               min={0.0} step={0.01}
               onChange={(e) =>
                 this.handleDataChange({ amount: e.target.value })
               }
        />
        <select className="currency-converter-item"
                onChange={(e) =>
                  this.handleDataChange({ currencyFrom: e.target.value })
                }>
          {currencyOptionsFrom.map(currency => (
            <option key={currency} value={currency}
            >{currency}</option>
          ))}
        </select>
        <select className="currency-converter-item"
                onChange={(e) =>
                  this.handleDataChange({ currencyTo: e.target.value })
                }>
          {currencyOptionsTo.map(currency => (
            <option key={currency} value={currency}>{currency}</option>
          ))}
        </select>
        <div className="result currency-converter-item">{calcResult}</div>
      </div>
    )
  }
}

export default CurrencyConverter
