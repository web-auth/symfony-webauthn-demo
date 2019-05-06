module.exports = {
    root: true,
    parser: 'babel-eslint',
    env: {
        es6: true,
        node: true,
        browser: true,
    },
    parserOptions: {
        ecmaVersion: 2018,
        sourceType: 'module',
        ecmaFeatures: {
            jsx: true,
        },
    },
    plugins: [ 'react' ],
    extends: [
        'eslint:recommended',
        'react-app',
        'plugin:react/recommended',
    ],
    rules: {
        'code': 120,
        quotes: [ 'error', 'single' ],
        'no-console': 'warn',
        'react/jsx-uses-react': 'error',
        'react/jsx-uses-vars': 'error',

        'array-bracket-spacing': [ 'error', 'always' ],
        'arrow-parens': [ 'error', 'as-needed' ],
        'arrow-spacing': [
            'error',
            {
                before: true,
                after: true,
            },
        ],
        'block-spacing': [ 'error' ],
        'brace-style': [ 'error', '1tbs' ],
        'comma-dangle': [ 'error', 'always-multiline' ],
        'comma-spacing': [
            'error',
            {
                before: false,
                after: true,
            },
        ],
        'eol-last': [ 'error', 'unix' ],
        eqeqeq: [ 'error' ],
        'func-call-spacing': [ 'error' ],
        indent: [ 'error', 4 ],
        'key-spacing': [
            'error',
            {
                beforeColon: false,
                afterColon: true,
            },
        ],
        'keyword-spacing': [
            'error',
            {
                after: true,
                before: true,
            },
        ],
        'linebreak-style': [ 'error', 'unix' ],
        'no-mixed-spaces-and-tabs': 2,
        'no-multiple-empty-lines': [
            'error',
            {
                max: 1,
            },
        ],
        'no-trailing-spaces': [ 'error' ],
        'no-var': [ 'warn' ],
        'object-curly-newline': [
            'error',
            {
                ObjectExpression: {
                    consistent: true,
                    minProperties: 2,
                    multiline: true,
                },
                ObjectPattern: {
                    consistent: true,
                    multiline: true,
                },
                ImportDeclaration: {
                    consistent: true,
                    multiline: true,
                },
                ExportDeclaration: {
                    consistent: true,
                    minProperties: 2,
                    multiline: true,
                },
            },
        ],
        'object-curly-spacing': [ 'error', 'never' ],
        'object-property-newline': [ 'error' ],
        'semi-spacing': [
            'error',
            {
                before: false,
                after: true,
            },
        ],
        'space-before-function-paren': [
            'error',
            {
                anonymous: 'always',
                asyncArrow: 'always',
                named: 'never',
            },
        ],
        'space-in-parens': [
            'error',
            'never',
        ],
        'space-unary-ops': [
            'error',
            {
                words: true,
                nonwords: false,
                overrides: {
                    '!': false,
                },
            },
        ],
        yoda: [ 'error', 'never' ],
        'react/jsx-curly-spacing': [
            'error',
            {
                when: 'never',
                children: false,
            },
        ],
        'react/jsx-wrap-multilines': [ 'error' ],
        'jsx-a11y/anchor-is-valid': [ 'error' ],
    },
    settings: {
        react: {
            createClass: 'createReactClass',
            pragma: 'React',
            version: 'detect',
            flowVersion: '0.53',
        },
        propWrapperFunctions: [
            'forbidExtraProps',
            {
                property: 'freeze',
                object: 'Object',
            },
            { property: 'myFavoriteWrapper' },
        ],
        linkComponents: [
            'Hyperlink',
            {
                name: 'Link',
                linkAttribute: 'to',
            },
        ],
    },
};
