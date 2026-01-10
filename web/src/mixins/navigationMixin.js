import {useActiveAreaStore} from 'stores/active-area';

export const navigationMixin = {
  methods: {
    navigateTo: function (route, isSidebarActive = false) {
      if (isSidebarActive) {
        useActiveAreaStore().setSidebarActiveArea();
      } else {
        useActiveAreaStore().setWorkspaceActiveArea();
      }
      this.$router.push({name: route})
    }
  }
}
