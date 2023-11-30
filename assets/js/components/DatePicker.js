import React, {Component} from 'react';

class DatePicker extends Component {
  render() {
    return (
        <div>
          <input
              type="date"
              value={this.props.selectedDate}
              onChange={(e) => this.props.onDateChange(e)}
              className="date-picker"
              name="datePicker"
              min="2023-01-01"
              max={new Date().toISOString().split('T')[0]}
              disabled={this.props.disabled}
          />
          <label className="date-picker-label" htmlFor="datePicker">Wybierz
            datę</label>
        </div>
    );
  }
}

export default DatePicker;
