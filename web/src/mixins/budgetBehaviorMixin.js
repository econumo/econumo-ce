export const budgetBehaviorMixin = {
  created: function () {
    // window.addEventListener('load', this.adjustBudgetTableWidth);
    window.addEventListener('resize', this.adjustBudgetTableWidth);
    window.addEventListener('keydown', this.onKeyDown);
  },
  beforeDestroy: function () {
    // window.removeEventListener('load', this.adjustBudgetTableWidth);
    window.removeEventListener('resize', this.adjustBudgetTableWidth);
    window.removeEventListener('keydown', this.onKeyDown);
  },
  data() {
    return {
      budgetView: {
        loadMonths: 3,
        showMonths: 3,
      },
      cursor: {
        point: {
          x: 1,
          y: 1
        },
        active: false
      },
    }
  },
  methods: {
    adjustBudgetTableWidth: function () {
      for (let i = this.budgetView.loadMonths; i > 0; i--) {
        if (this.checkIfVisible('.js-budget-container-month-' + i)) {
          this.budgetView.showMonths = i;
          if (i < this.cursor.point.x) {
            this.cursor.point.x = i;
          }
          break;
        }
      }
    },
    getElement: function (selector) {
      return window.document.querySelector(selector);
    },
    getElementPosition: function (selector) {
      const element = this.getElement(selector);
      if (element) {
        return element.getBoundingClientRect();
      }
      return null
    },
    checkIfVisible: function (selector) {
      const position = this.getElementPosition(selector);
      if (!position) {
        return false;
      }
      return this.checkVisible(position, 50);
    },
    checkVisible: function (rect, threshold, mode) {
      threshold = threshold || 0;
      mode = mode || 'visible';

      const viewHeight = Math.max(document.documentElement.clientHeight, window.innerHeight);
      const above = rect.bottom - threshold < 0;
      const below = rect.top - viewHeight + threshold >= 0;

      return mode === 'above' ? above : (mode === 'below' ? below : !above && !below);
    },

    // Cursor manipulations
    checkIfCursorVisible: function () {
      const position = this.getElementPosition('.current');
      if (!position) {
        return;
      }
      if (position.top < position.height || position.bottom > window.innerHeight - position.height) {
        window.scroll(position.left - position.height, position.bottom - position.height);
      }
    }
    ,
    setCursor: function (x, y, checkIfVisible) {
      this.stopEditing();
      this.cursor.point.x = x;
      this.cursor.point.y = y;
      if (checkIfVisible) {
        this.checkIfCursorVisible();
      }
    },
    startEditing: function (x, y) {
      this.setCursor(x, y);
      for (let i in this.categories) {
        if (this.categories[i].position === y && this.categories[i].permissions.canEdit && this.categories[i].level > 0) {
          this.cursor.active = true;
          return;
        }
      }
    },
    stopEditing: function () {
      if (this.cursor.active) {
        this.cursor.active = false;
      }
    },
    onKeyDown: function (e) {
      if (e.key === 'Enter') {
        if (this.cursor.active) {
          this.stopEditing();
        } else {
          this.startEditing(this.cursor.point.x, this.cursor.point.y);
        }

      } else if (e.key === 'Escape') {
        this.stopEditing();

      } else if (e.key === 'ArrowDown' && !this.cursor.active) {
        if (this.cursor.point.y < this.categories.length + 2) {
          this.setCursor(this.cursor.point.x, ++this.cursor.point.y, true)
        }

      } else if (e.key === 'ArrowUp' && !this.cursor.active) {
        if (this.cursor.point.y > 0) {
          this.setCursor(this.cursor.point.x, --this.cursor.point.y, true)
        }

      } else if (e.key === 'ArrowLeft' && !this.cursor.active) {
        if (this.cursor.point.x > 0) {
          this.setCursor(--this.cursor.point.x, this.cursor.point.y, true)
        }

      } else if (e.key === 'ArrowRight' && !this.cursor.active) {
        if (this.cursor.point.x < this.budgetView.showMonths) {
          this.setCursor(++this.cursor.point.x, this.cursor.point.y, true)
        }

      } else {
        return;
      }

      e.preventDefault();
    },
  }
};
