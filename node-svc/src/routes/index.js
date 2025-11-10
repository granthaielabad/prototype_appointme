import express from "express"
import smsRouter from "./sms.js"
import paymentsRouter from "./payment.js"

const router = express.Router()

router.use("/sms", smsRouter) 

router.use("/", paymentsRouter)




export default router;

