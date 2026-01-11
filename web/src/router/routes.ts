import { RouteRecordRaw } from 'vue-router';
import { RouterPage } from './constants';
import LoginLayout from '../layouts/LoginLayout.vue';
import ApplicationLayout from '../layouts/ApplicationLayout.vue';
import Login from '../pages/Login.vue';
import Logout from '../pages/Logout.vue';
import Account from '../pages/Account.vue';
import Registration from 'pages/Registration.vue';
import Settings from 'pages/Settings/Settings.vue';
import SettingsProfile from 'pages/Settings/Profile.vue';
import SettingsChangePassword from 'pages/Settings/ChangePassword.vue';
import SettingsAccounts from 'pages/Settings/Accounts.vue';
import SettingsCategories from 'pages/Settings/Categories.vue';
import SettingsPayees from 'pages/Settings/Payees.vue';
import SettingsBudgets from 'pages/Settings/Budgets.vue';
import SettingsTags from 'pages/Settings/Tags.vue';
import SettingsConnections from 'pages/Settings/Connections.vue';
import Budget from 'pages/Budget/Budget.vue';
import Onboarding from 'pages/Onboarding.vue';
import Home from 'pages/Home.vue';

const routes: RouteRecordRaw[] = [];

routes.push({
  path: '/login',
  component: LoginLayout,
  children: [
    { path: '', name: RouterPage.LOGIN, component: Login, meta: { requireAuth: false } }
  ]
});

routes.push({
  path: '/register',
  component: LoginLayout,
  children: [
    { path: '', name: RouterPage.REGISTER, component: Registration, meta: { requireAuth: false } }
  ]
});

routes.push({
  path: '/logout',
  name: RouterPage.LOGOUT,
  component: Logout,
  meta: {
    requireAuth: false
  }
});

routes.push({
  path: '/',
  component: ApplicationLayout,
  children: [
    { 
      path: '', 
      name: RouterPage.HOME,
      component: Home,
      meta: { requireAuth: true } 
    },
    { path: '/account/:id', name: RouterPage.ACCOUNT, component: Account, meta: { requireAuth: true } }
  ]
});

routes.push({
  path: '/budget',
  component: ApplicationLayout,
  children: [
    { path: '', name: RouterPage.BUDGET, component: Budget, meta: { requireAuth: true } }
  ]
});

routes.push({
  path: '/onboarding',
  component: ApplicationLayout,
  children: [
    { path: '', name: RouterPage.ONBOARDING, component: Onboarding, meta: { requireAuth: true } }
  ]
});

const settingsRoutes: RouteRecordRaw[] = [];
settingsRoutes.push({ path: '', name: RouterPage.SETTINGS, component: Settings, meta: { requireAuth: true } });
settingsRoutes.push({
  path: '/settings/profile',
  name: RouterPage.SETTINGS_PROFILE,
  component: SettingsProfile,
  meta: { requireAuth: true }
});
settingsRoutes.push({
  path: '/settings/profile/change-password',
  name: RouterPage.SETTINGS_CHANGE_PASSWORD,
  component: SettingsChangePassword,
  meta: { requireAuth: true }
});
settingsRoutes.push({
  path: '/settings/accounts',
  name: RouterPage.SETTINGS_ACCOUNTS,
  component: SettingsAccounts,
  meta: { requireAuth: true }
});
settingsRoutes.push({
  path: '/settings/categories',
  name: RouterPage.SETTINGS_CATEGORIES,
  component: SettingsCategories,
  meta: { requireAuth: true }
});
settingsRoutes.push({
  path: '/settings/payees',
  name: RouterPage.SETTINGS_PAYEES,
  component: SettingsPayees,
  meta: { requireAuth: true }
});
settingsRoutes.push({
  path: '/settings/tags',
  name: RouterPage.SETTINGS_TAGS,
  component: SettingsTags,
  meta: { requireAuth: true }
});
settingsRoutes.push({
  path: '/settings/connections',
  name: RouterPage.SETTINGS_CONNECTIONS,
  component: SettingsConnections,
  meta: { requireAuth: true }
});
settingsRoutes.push({
  path: '/settings/budgets',
  name: RouterPage.SETTINGS_BUDGETS,
  component: SettingsBudgets,
  meta: { requireAuth: true }
});
routes.push({
  path: '/settings',
  name: RouterPage.SETTINGS_SETTINGS,
  component: ApplicationLayout,
  children: settingsRoutes,
  meta: { requireAuth: true }
});

// Always leave this as last one,
// but you can also remove it
routes.push(
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/Error404.vue')
  });

export default routes;
