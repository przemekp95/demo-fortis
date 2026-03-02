module.exports = {
    root: true,
    env: {
        browser: true,
        es2022: true,
        node: true,
    },
    globals: {
        route: 'readonly',
        Ziggy: 'readonly',
    },
    extends: ['eslint:recommended', 'plugin:vue/vue3-essential'],
    parserOptions: {
        ecmaVersion: 'latest',
        sourceType: 'module',
    },
    rules: {
        'vue/multi-word-component-names': 'off',
        'vue/html-indent': 'off',
        'vue/max-attributes-per-line': 'off',
        'vue/attributes-order': 'off',
        'vue/html-self-closing': 'off',
        'vue/singleline-html-element-content-newline': 'off',
        'vue/html-closing-bracket-newline': 'off',
        'vue/require-prop-types': 'off',
    },
};
