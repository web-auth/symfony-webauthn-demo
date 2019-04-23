module.exports = {
    parser: "babel-eslint",
    env: {
        es6: true,
        node: true,
        browser: true
    },
    parserOptions: {
        ecmaVersion: 6,
        sourceType: "module",
        ecmaFeatures: {
            jsx: true
        }
    },
    rules: {
        "react/jsx-uses-react": "error",
        "react/jsx-uses-vars": "error"
    },
    plugins: ["react"],
    extends: [
        "eslint:all",
        "plugin:react/all",
        "plugin:prettier/recommended"
    ],
    settings: {
        react: {
            pragma: "React",
            version: "detect"
        },
        propWrapperFunctions: [
            "forbidExtraProps",
            { property: "freeze", object: "Object" },
            { property: "myFavoriteWrapper" }
        ],
        linkComponents: ["Hyperlink", { name: "Link", linkAttribute: "to" }]
    }
};
