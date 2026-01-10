import { route } from 'quasar/wrappers';
import {
  createMemoryHistory,
  createRouter,
  createWebHashHistory,
  createWebHistory,
} from 'vue-router';
import routes from './routes';
import {useUsersStore} from 'stores/users';
import {METRICS, trackEvent} from '../modules/metrics'

/*
 * If not building with SSR mode, you can
 * directly export the Router instantiation;
 *
 * The function below can be async too; either use
 * async/await or return a Promise which resolves
 * with the Router instance.
 */

export default route(function ( { store}) {
  const createHistory = process.env.SERVER
    ? createMemoryHistory
    : (process.env.VUE_ROUTER_MODE === 'history' ? createWebHistory : createWebHashHistory);

  const Router = createRouter({
    scrollBehavior: () => ({ left: 0, top: 0 }),
    routes,

    // Leave this as is and make changes in quasar.conf.js instead!
    // quasar.conf.js -> build -> vueRouterMode
    // quasar.conf.js -> build -> publicPath
    history: createHistory(
      process.env.VUE_ROUTER_BASE
    ),
  });

  Router.beforeEach((to, from, next) => {
    trackEvent(METRICS.PAGE_VIEW, {from: from?.name ?? null, to: to?.name});
    if (to.matched.some(record => record.meta.requireAuth) && !useUsersStore().isUserAuthenticated) {
      next({ name: 'login', query: { next: to.fullPath } })
    } else {
      next()
    }
  })

  return Router;
});
