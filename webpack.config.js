const path = require("path")

const BrowserSyncPlugin = require("browser-sync-webpack-plugin")
const MiniCssExtractPlugin = require("mini-css-extract-plugin")
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin")

module.exports = {
  entry: "./src/js/index.js",
  output: {
    path: path.resolve(__dirname, "dist"), // * path.resolve()  => 返回拼接的絕對路徑, __dirname => 當前檔案(模塊)的目錄路徑
    filename: "bundle.js",
    clean: true, // ! 重新編譯前刪除打包之前的文件
  },
  devtool: "inline-source-map", // can find source code error
  mode: "development",
  module: {
    rules: [
      {
        test: /\.(?:js|mjs|cjs)$/,
        exclude: /node_modules/,
        use: {
          loader: "babel-loader",
          options: {
            presets: [["@babel/preset-env", { targets: "defaults" }]],
          },
        },
      },

      {
        test: /\.css$/i,
        exclude: /node_modules/,
        use: [
          /*"style-loader",*/
          MiniCssExtractPlugin.loader,
          "css-loader",
          "postcss-loader",
        ], // * parse css needs
      },

      {
        test: /\.(png|svg|jpg|jpeg|gif)$/i, // * parse image needs
        type: "asset/resource", // built-in
      },

      {
        test: /\.(woff|woff2|eot|ttf|otf)$/i,
        type: "asset/resource",
      },
    ],
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: "bundle.css",
    }),
    // ! 實現瀏覽器hot reload browser-sync(可搭配 devServer 或者直接使用watch 2選1,  這邊是使用watch)
    new BrowserSyncPlugin({
      port: 3000,
      host: "localhost",
      // server or proxy 只能選一個
      // server: { baseDir: ["dist"] },
      proxy: "localhost/wordpress",
      files: ["src/**/*.js", "src/**/*.css", "src/**/*.php"],
    }),
  ],
  optimization: {
    minimizer: [new CssMinimizerPlugin()],
    minimize: false, // true is development mode enable minimizer
  },
}
