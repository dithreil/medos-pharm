/* eslint-env node */
// eslint-disable-next-line @typescript-eslint/no-var-requires

module.exports = {
    'presets': [
        '@babel/preset-env',
    ],
    'plugins': ['@babel/plugin-syntax-dynamic-import'],
    'env': {
        'test': {
            'presets': [
                '@babel/preset-env',
            ],
            'plugins': ['@babel/plugin-syntax-dynamic-import'],
        },
    },
    'use': {
        'loader': 'babel-loader',
        'options': {
            'presets': ['@babel/preset-env'],
        },
    },
};
