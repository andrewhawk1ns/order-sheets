module.exports = {
    verbose:true,
    roots: ["<rootDir>/resources/js", "<rootDir>/resources/js/tests/"],
    moduleFileExtensions: ["js", "vue"],
    moduleNameMapper: {
      '^@/(.*)$': '<rootDir>/resources/js/$1',
    },
    transform: {
      "^.+\\.js$": "babel-jest",
      ".*\\.(vue)$": "vue-jest"
    },
}

