import { date } from 'quasar'
const { formatDate, extractDate } = date

export default {
  methods: {
    toQuasarDate(timestamp) {
      return formatDate(timestamp, 'YYYY/MM/DD')
    },

    toQuasarDateTime(timestamp) {
      return formatDate(timestamp, 'YYYY/MM/DD HH:mm:ss')
    },

    toLocalDateTime(timestamp) {
      return formatDate(timestamp, 'DD.MM.YYYY HH:mm:ss')
    },

    toLocalDowDateTime(timestamp) {
      return formatDate(timestamp, 'dddd, DD.MM.YYYY HH:mm:ss')
    },

    toLocalDate(timestamp) {
      return formatDate(timestamp, 'DD.MM.YYYY')
    },

    toLocalDowDate(timestamp) {
      return formatDate(timestamp, 'dddd, DD.MM.YYYY')
    },

    toLocalTime(timestamp) {
      return formatDate(timestamp, 'HH:mm:ss')
    },

    fromLocalDateTime(string) {
      if (string.indexOf(':') > 0) {
        return extractDate(string, 'DD.MM.YYYY HH:mm:ss')
      }
      return extractDate(string + ' 00:00:00', 'DD.MM.YYYY HH:mm:ss')
    },

    toISODate(timestamp) {
      const format = 'YYYY-MM-DD'
      let ret = formatDate(timestamp, format)

      if (ret === undefined) {
        return formatDate(this.fromLocalDateTime(timestamp), format)
      }

      return ret
    },

    toISODateTime(timestamp) {
      const format = 'YYYY-MM-DD HH:mm:ss'
      let ret = formatDate(timestamp, format)

      if (ret === undefined) {
        return formatDate(this.fromLocalDateTime(timestamp), format)
      }

      return ret
    },

    age(birthDate, referenceDate = null) {
      if (!referenceDate) {
        referenceDate = new Date()
      }

      let age = referenceDate.getFullYear() - birthDate.getFullYear()

      if (age < 0) {
        return 0
      }

      let months = referenceDate.getMonth() - birthDate.getMonth()
      let days = referenceDate.getDate() - birthDate.getDate()

      if (months < 0 || (!months && days < 0)) {
        return age - 1
      }

      return age
    }
  }
}
