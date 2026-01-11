import {defineStore} from 'pinia';
import AnalyticsAPIv1 from '../modules/api/v1/analytics';
import {date} from 'quasar';

export const useBalanceAnalyticsStore = defineStore('balance-analytics', {
  state: () => ({
    balanceAnalyticsLoaded: false,
    balanceAnalyticsLoadedAt: null as string | null,
    balanceAnalytics: [],
  }),
  getters: {
    isBalanceAnalyticsLoaded: state => state.balanceAnalyticsLoaded,
    // balanceAnalytics: state => state.balanceAnalytics,
    // balanceAnalyticsLoadedAt: state => state.balanceAnalyticsLoadedAt,
  },
  actions: {
    fetchBalanceAnalytics(params: { from: string, to: string }) {
      return AnalyticsAPIv1.getBalance(params.from, params.to, (response: any) => {
        this.BALANCE_ANALYTICS_INIT(response.data.items);
      }, (error = {}) => {
        return error
      })
    },
    CONNECTIONS_LOADED(value: boolean) {
      this.balanceAnalyticsLoaded = value;
    },
    BALANCE_ANALYTICS_INIT(items = []) {
      this.balanceAnalytics = items;
      this.balanceAnalyticsLoadedAt = date.formatDate(new Date(), 'YYYY-MM-DD HH:mm:ss');
      this.balanceAnalyticsLoaded = true;
    },
  },
});
