import axios from 'axios';

class CurrencyService {
  async fetchCurrencies(date) {
    const baseUrl = 'http://telemedi-zadanie.localhost';
    const url = new URL(`/api/exchange-rates`, baseUrl);

    if (date) {
      url.searchParams.append('date', date);
    }

    return await axios.get(url.toString()).
        then(response => response.data).
        catch((err) => {
          console.error(err);
        });
  }
}

export default CurrencyService;