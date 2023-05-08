import "../css/index.css"
import "./another.js"
import { initWoocommercePayment } from "./payment"
const nums = [1, 2, 3]
const newNums = nums.map((num) => num * 2)
console.log(newNums)

initWoocommercePayment()
