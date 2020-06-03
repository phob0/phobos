import { debounce, scroll } from 'quasar'
const { getScrollTarget, setScrollPosition } = scroll

export default {
  data() {
    return {
      activeTab: ''
    }
  },

  methods: {
    /**
     * Scrolls to the given section
     */
    gotoSection(section) {
      const el = document.getElementById(`section-${section}`)
      setScrollPosition(getScrollTarget(el), el.offsetTop, 250)
    },

    /**
     * Scrolls to the top of the screen
     */
    gotoTop() {
      const el = document.getElementById('section-client') // just picked one
      setScrollPosition(getScrollTarget(el), 0, 250)
    },

    /**
     * Sets the active tab, depending on the current scroll position
     * @returns {Function}
     */
    setActiveTab() {
      return debounce(position => {
        for (let i of document.getElementsByClassName('q-card__section')) {
          const id = i.getAttribute('id')
          if (!id) {
            continue
          }
          const idParts = id.match(/^section-(.+)$/)
          if (idParts.length < 2) {
            continue
          }

          if (i.offsetTop + i.offsetHeight > position) {
            this.activeTab = idParts[1]
            return
          }
        }
      }, 250)
    }
  }
}
