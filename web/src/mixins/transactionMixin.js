import _ from 'lodash';
import {date} from 'quasar'

export const transactionMixin = {
  methods: {
    filterByAccount: function (transactions, accountId) {
      return _.filter(transactions, (item) => {
        return item.accountId === accountId || item.accountRecipientId === accountId;
      });
    },
    transactionsDailyList: function (transactions, fieldName = 'date') {
      const items = _.orderBy(transactions, fieldName, 'desc');
      const today = new Date(),
        yesterday = date.subtractFromDate(today, {days: 1}),
        tomorrow = date.startOfDate(date.addToDate(today, {days: 1}), 'date');
      let result = [],
        currentDate = '';
      _.forEach(items, function (item) {
          const itemDate = date.extractDate(item[fieldName], 'YYYY-MM-DD HH:mm:ss'),
            day = date.formatDate(itemDate, 'YYYY-MM-DD'),
            dayFormatted = date.formatDate(itemDate, 'Do MMMM YYYY');


          let dateAlias = 'none';
          if (day === date.formatDate(today, 'YYYY-MM-DD')) {
            dateAlias = 'today';
          } else if (day === date.formatDate(yesterday, 'YYYY-MM-DD')) {
            dateAlias = 'yesterday';
          }

          if(currentDate !== day) {
            currentDate = day;
            result.push({
              id: day,
              isSeparator: true,
              alias: dateAlias,
              date: dayFormatted,
            });
          }
          item.isInFuture = itemDate >= tomorrow;
          result.push(item);
        }
      );
      return result
    }
  }
}
