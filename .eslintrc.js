const {resolve} = require('path');
module.exports = {
    // https://eslint.org/docs/user-guide/configuring#configuration-cascading-and-hierarchy
    // This option interrupts the configuration hierarchy at this file
    // Remove this if you have an higher level ESLint config file (it usually happens into a monorepos)
    root: true,

    // https://eslint.vuejs.org/user-guide/#how-to-use-custom-parser
    // Must use parserOptions instead of "parser" to allow vue-eslint-parser to keep working
    // `parser: 'vue-eslint-parser'` is already included with any 'plugin:vue/**' config and should be omitted
    parserOptions: {
    // https://github.com/typescript-eslint/typescript-eslint/tree/master/packages/parser#configuration
    // https://github.com/TypeStrong/fork-ts-checker-webpack-plugin#eslint
    // Needed to make the parser take into account 'vue' files
        extraFileExtensions: ['.vue'],
        warnOnUnsupportedTypeScriptVersion: false,
        parser: '@typescript-eslint/parser',
        project: './tsconfig.json',
        tsconfigRootDir: __dirname,
        ecmaVersion: 2019, // Allows for the parsing of modern ECMAScript features
        sourceType: 'module', // Allows for the use of imports
        createDefaultProgram: true,
    },

    // ignorePatterns: ['vendor/*'],
    env: {
        browser: true,
    },

    // Rules order is important, please avoid shuffling them
    extends: [
        // Base ESLint recommended rules
        // 'eslint:recommended',
        'google',
        // https://github.com/typescript-eslint/typescript-eslint/tree/master/packages/eslint-plugin#usage
        // ESLint typescript rules
        'plugin:@typescript-eslint/recommended',
        // consider disabling this class of rules if linting takes too long
        // 'plugin:@typescript-eslint/recommended-requiring-type-checking',

        // Uncomment any of the lines below to choose desired strictness,
        // but leave only one uncommented!
        // See https://eslint.vuejs.org/rules/#available-rules
        'plugin:vue/essential', // Priority A: Essential (Error Prevention)
        // 'plugin:vue/strongly-recommended', // Priority B: Strongly Recommended (Improving Readability)
        // 'plugin:vue/recommended', // Priority C: Recommended (Minimizing Arbitrary Choices and Cognitive Overhead)


    ],

    plugins: [
    // required to apply rules which need type information
        '@typescript-eslint',

        // https://eslint.vuejs.org/user-guide/#why-doesn-t-it-work-on-vue-file
        // required to lint *.vue files
        'vue',
    ],

    globals: {
        __statics: true,
        process: true,
        Capacitor: true,
        chrome: true,
    },

    // add your custom rules here
    rules: {
    // allow async-await
        'generator-star-spacing': 'off',
        'one-var': 'off',
        'max-len': ['error', {'code': 120}],
        'new-cap': 'off',
        'require-jsdoc': 'off',
        'import/first': 'off',
        'import/named': 'off',
        'import/namespace': 'off',
        'import/default': 'off',
        'import/export': 'off',
        'import/extensions': 'off',
        'import/no-unresolved': 'off',
        'import/no-extraneous-dependencies': 'off',
        'prefer-promise-reject-errors': 'off',
        'space-before-function-paren': ['error', 'never'],
        'semi': ['error', 'always'],
        'comma-dangle': ['error', 'always-multiline'],
        'indent': ['error', 4],
        'object-curly-spacing': ['error', 'never'],
        'array-bracket-spacing': ['error', 'never'],
        'operator-linebreak': ['error', 'before'],
        'linebreak-style': 'off',
        'yoda': ['error', 'always'],
        'arrow-parens': ['error', 'always'],
        'newline-before-return': 'error',
        'no-unreachable': 'error',

        // TypeScript
        'quotes': ['warn', 'single', {avoidEscape: true}],
        '@typescript-eslint/explicit-function-return-type': 'off',
        '@typescript-eslint/explicit-module-boundary-types': 'off',
        '@typescript-eslint/no-unsafe-member-access': 'off',
        '@typescript-eslint/no-extra-semi': 'off',
        '@typescript-eslint/no-explicit-any': 'off',
        '@typescript-eslint/no-unsafe-assignment': 'off',
        '@typescript-eslint/no-floating-promises': 'off',
        '@typescript-eslint/no-unsafe-call': 'off',
        '@typescript-eslint/no-unsafe-return': 'off',
        '@typescript-eslint/ban-ts-comment': 'off',
        '@typescript-eslint/await-thenable': 'off',
        // allow debugger during development only
        'no-debugger': 'production' === process.env.NODE_ENV ? 'error' : 'off',
    },
};
